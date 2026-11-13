<?php

namespace App\Support;

/**
 * Лёгкий разбор MP4 на PHP (без ffmpeg): размер кадра, длительность и наличие видео/звука читаются из блока moov.
 * Нужен, чтобы не верить числам, которые прислал браузер, и отсекать слишком тяжёлые ролики на сервере.
 */
final class Mp4Info
{
    /** moov больше этого размера не читаем (у обычных роликов он в пределах мегабайта). */
    private const MAX_MOOV_BYTES = 16 * 1024 * 1024;

    /**
     * @return array{duration: float, width: int, height: int, has_video: bool, has_audio: bool, faststart: bool}|null
     *         null, если файл не похож на корректный MP4 с блоком moov
     */
    public static function read(string $path): ?array
    {
        $size = @filesize($path);
        $handle = @fopen($path, 'rb');
        if ($handle === false || ! $size) {
            return null;
        }

        try {
            $moov = null;
            $moovAt = null;
            $mdatAt = null;
            $pos = 0;
            $guard = 0;

            while ($pos + 8 <= $size && $guard++ < 10000) {
                fseek($handle, $pos);
                $head = (string) fread($handle, 16);
                if (strlen($head) < 8) {
                    break;
                }

                $boxSize = (int) unpack('N', substr($head, 0, 4))[1];
                $type = substr($head, 4, 4);
                $headerSize = 8;
                if ($boxSize === 1) {
                    if (strlen($head) < 16) {
                        return null;
                    }
                    $boxSize = (int) unpack('J', substr($head, 8, 8))[1];
                    $headerSize = 16;
                } elseif ($boxSize === 0) {
                    $boxSize = $size - $pos;
                }

                if ($boxSize < $headerSize || $pos + $boxSize > $size) {
                    return null;
                }

                if ($type === 'moov' && $moov === null) {
                    $length = $boxSize - $headerSize;
                    if ($length <= 0 || $length > self::MAX_MOOV_BYTES) {
                        return null;
                    }
                    fseek($handle, $pos + $headerSize);
                    $moov = (string) fread($handle, $length);
                    $moovAt = $pos;
                } elseif ($type === 'mdat' && $mdatAt === null) {
                    $mdatAt = $pos;
                }

                $pos += $boxSize;
            }

            if ($moov === null) {
                return null;
            }

            return self::parseMoov($moov, $moovAt !== null && ($mdatAt === null || $moovAt < $mdatAt));
        } finally {
            fclose($handle);
        }
    }

    /**
     * @return array{duration: float, width: int, height: int, has_video: bool, has_audio: bool, faststart: bool}|null
     */
    private static function parseMoov(string $moov, bool $faststart): ?array
    {
        $duration = 0.0;
        $width = 0;
        $height = 0;
        $hasVideo = false;
        $hasAudio = false;

        foreach (self::children($moov) as [$type, $body]) {
            if ($type === 'mvhd') {
                $duration = self::movieDuration($body);
            } elseif ($type === 'trak') {
                $track = self::parseTrack($body);
                if ($track['handler'] === 'vide' && ! $hasVideo) {
                    $hasVideo = true;
                    $width = $track['width'];
                    $height = $track['height'];
                } elseif ($track['handler'] === 'soun') {
                    $hasAudio = true;
                }
            }
        }

        if (! $hasVideo || $width <= 0 || $height <= 0) {
            return null;
        }

        return [
            'duration' => $duration,
            'width' => $width,
            'height' => $height,
            'has_video' => true,
            'has_audio' => $hasAudio,
            'faststart' => $faststart,
        ];
    }

    /**
     * @return array{handler: string, width: int, height: int}
     */
    private static function parseTrack(string $trak): array
    {
        $out = ['handler' => '', 'width' => 0, 'height' => 0];

        foreach (self::children($trak) as [$type, $body]) {
            if ($type === 'tkhd') {
                $dims = self::trackDimensions($body);
                $out['width'] = $dims[0];
                $out['height'] = $dims[1];
            } elseif ($type === 'mdia') {
                foreach (self::children($body) as [$sub, $subBody]) {
                    if ($sub === 'hdlr' && strlen($subBody) >= 12) {
                        $out['handler'] = substr($subBody, 8, 4);
                    }
                }
            }
        }

        return $out;
    }

    private static function movieDuration(string $mvhd): float
    {
        if ($mvhd === '') {
            return 0.0;
        }

        $version = ord($mvhd[0]);
        if ($version === 1 && strlen($mvhd) >= 32) {
            $timescale = (int) unpack('N', substr($mvhd, 20, 4))[1];
            $units = (int) unpack('J', substr($mvhd, 24, 8))[1];
        } elseif ($version === 0 && strlen($mvhd) >= 20) {
            $timescale = (int) unpack('N', substr($mvhd, 12, 4))[1];
            $units = (int) unpack('N', substr($mvhd, 16, 4))[1];
        } else {
            return 0.0;
        }

        return $timescale > 0 ? $units / $timescale : 0.0;
    }

    /**
     * Ширина и высота кадра из tkhd (с учётом поворота на 90/270 градусов в матрице).
     *
     * @return array{0: int, 1: int}
     */
    private static function trackDimensions(string $tkhd): array
    {
        if ($tkhd === '') {
            return [0, 0];
        }

        $version = ord($tkhd[0]);
        $matrixAt = $version === 1 ? 52 : 40;
        $sizeAt = $matrixAt + 36;
        if (strlen($tkhd) < $sizeAt + 8) {
            return [0, 0];
        }

        $w = (int) round(((int) unpack('N', substr($tkhd, $sizeAt, 4))[1]) / 65536);
        $h = (int) round(((int) unpack('N', substr($tkhd, $sizeAt + 4, 4))[1]) / 65536);

        // Матрица: a b u c d v x y w (по 4 байта). Поворот на 90/270: a = d = 0, b и c не нули.
        $a = self::signed(substr($tkhd, $matrixAt, 4));
        $d = self::signed(substr($tkhd, $matrixAt + 16, 4));
        if ($a === 0 && $d === 0) {
            return [$h, $w];
        }

        return [$w, $h];
    }

    private static function signed(string $four): int
    {
        $v = (int) unpack('N', $four)[1];

        return $v >= 0x80000000 ? $v - 0x100000000 : $v;
    }

    /**
     * Вложенные блоки: [тип, тело].
     *
     * @return list<array{0: string, 1: string}>
     */
    private static function children(string $data): array
    {
        $out = [];
        $len = strlen($data);
        $pos = 0;

        while ($pos + 8 <= $len) {
            $boxSize = (int) unpack('N', substr($data, $pos, 4))[1];
            $type = substr($data, $pos + 4, 4);
            $headerSize = 8;
            if ($boxSize === 1) {
                if ($pos + 16 > $len) {
                    break;
                }
                $boxSize = (int) unpack('J', substr($data, $pos + 8, 8))[1];
                $headerSize = 16;
            } elseif ($boxSize === 0) {
                $boxSize = $len - $pos;
            }

            if ($boxSize < $headerSize || $pos + $boxSize > $len) {
                break;
            }

            $out[] = [$type, substr($data, $pos + $headerSize, $boxSize - $headerSize)];
            $pos += $boxSize;
        }

        return $out;
    }
}
