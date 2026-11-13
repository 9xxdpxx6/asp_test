<?php

namespace App\Support;

/**
 * Запасной серверный ресайз фото для слайдов (GD). Основной ресайз делает браузер админа до отправки;
 * сюда доходит только то, что обошло браузер: слишком большие кадры уменьшаются, фото с телефона поворачивается
 * по EXIF, лишние метаданные пропадают.
 */
final class ImageNormalizer
{
    /** Выше этого числа пикселей фото не открываем вовсе: GD съест гигабайты памяти. */
    public const MAX_PIXELS = 40_000_000;

    /**
     * @return array<string, string> mime => расширение
     */
    public static function supportedTypes(): array
    {
        $types = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        if (function_exists('imagecreatefromwebp') && function_exists('imagewebp')) {
            $types['image/webp'] = 'webp';
        }

        return $types;
    }

    /**
     * Читает размеры и тип без полной загрузки изображения.
     *
     * @return array{width: int, height: int, mime: string}|null
     */
    public static function inspect(string $path): ?array
    {
        $info = @getimagesize($path);
        if ($info === false || empty($info['mime']) || $info[0] < 1 || $info[1] < 1) {
            return null;
        }

        return ['width' => (int) $info[0], 'height' => (int) $info[1], 'mime' => (string) $info['mime']];
    }

    /**
     * Уменьшает фото до maxSide по длинной стороне (если больше) и записывает в $dest.
     * Меньшие фото копируются как есть. Возвращает итоговые размеры и расширение файла.
     *
     * @return array{width: int, height: int, ext: string}
     */
    public static function normalize(string $src, string $dest, int $maxSide, int $quality = 85): array
    {
        $info = self::inspect($src);
        $types = self::supportedTypes();
        if ($info === null || ! isset($types[$info['mime']])) {
            throw new \InvalidArgumentException('Неподдерживаемый формат изображения.');
        }
        if ($info['width'] * $info['height'] > self::MAX_PIXELS) {
            throw new \InvalidArgumentException('Фото слишком большое.');
        }

        $ext = $types[$info['mime']];
        $orientation = $info['mime'] === 'image/jpeg' ? self::exifOrientation($src) : 1;
        $swap = in_array($orientation, [5, 6, 7, 8], true);
        $width = $swap ? $info['height'] : $info['width'];
        $height = $swap ? $info['width'] : $info['height'];

        $needsResize = max($width, $height) > $maxSide;
        if (! $needsResize && $orientation === 1) {
            if (! @copy($src, $dest)) {
                throw new \RuntimeException('Не удалось сохранить фото.');
            }

            return ['width' => $width, 'height' => $height, 'ext' => $ext];
        }

        if (! extension_loaded('gd')) {
            // GD нет: оставляем как есть (размер уже ограничен проверками запроса)
            if (! @copy($src, $dest)) {
                throw new \RuntimeException('Не удалось сохранить фото.');
            }

            return ['width' => $width, 'height' => $height, 'ext' => $ext];
        }

        $image = match ($info['mime']) {
            'image/jpeg' => @imagecreatefromjpeg($src),
            'image/png' => @imagecreatefrompng($src),
            default => @imagecreatefromwebp($src),
        };
        if ($image === false) {
            throw new \InvalidArgumentException('Не удалось прочитать фото.');
        }

        $image = self::rotate($image, $orientation);

        $k = $needsResize ? $maxSide / max($width, $height) : 1.0;
        $newW = max(1, (int) round($width * $k));
        $newH = max(1, (int) round($height * $k));

        if ($newW !== $width || $newH !== $height) {
            $resized = imagecreatetruecolor($newW, $newH);
            if ($info['mime'] !== 'image/jpeg') {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
            }
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newW, $newH, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        $ok = match ($info['mime']) {
            'image/jpeg' => imagejpeg($image, $dest, $quality),
            'image/png' => imagepng($image, $dest, 6),
            default => imagewebp($image, $dest, $quality),
        };
        imagedestroy($image);

        if (! $ok) {
            throw new \RuntimeException('Не удалось сохранить фото.');
        }

        return ['width' => $newW, 'height' => $newH, 'ext' => $ext];
    }

    private static function exifOrientation(string $path): int
    {
        if (! function_exists('exif_read_data')) {
            return 1;
        }

        $exif = @exif_read_data($path);
        $value = is_array($exif) ? (int) ($exif['Orientation'] ?? 1) : 1;

        return ($value >= 1 && $value <= 8) ? $value : 1;
    }

    /**
     * @param  \GdImage  $image
     * @return \GdImage
     */
    private static function rotate($image, int $orientation)
    {
        $angle = match ($orientation) {
            3, 4 => 180,
            6, 5 => -90,
            8, 7 => 90,
            default => 0,
        };

        if ($angle !== 0) {
            $rotated = imagerotate($image, $angle, 0);
            if ($rotated !== false) {
                imagedestroy($image);
                $image = $rotated;
            }
        }

        if (in_array($orientation, [2, 4, 5, 7], true)) {
            imageflip($image, IMG_FLIP_HORIZONTAL);
        }

        return $image;
    }
}
