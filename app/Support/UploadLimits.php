<?php

namespace App\Support;

/**
 * Лимиты загрузки файлов, которые реально действуют на текущем сервере (php.ini).
 * Отдаются в админку, чтобы клиентский ресайзер ужимал фото до допустимого размера.
 */
class UploadLimits
{
    /**
     * Максимум для одного изображения по правилам валидации (max:5120 в FormRequest'ах).
     */
    public const APP_IMAGE_MAX_BYTES = 5 * 1024 * 1024;

    public static function forJs(): array
    {
        return [
            'uploadMax' => self::iniBytes('upload_max_filesize'),
            'postMax' => self::iniBytes('post_max_size'),
            'maxFiles' => (int) ini_get('max_file_uploads'),
            'appImageMax' => self::APP_IMAGE_MAX_BYTES,
        ];
    }

    /**
     * Переводит значение вида "8M" / "2G" / "512K" в байты. 0 или пусто — без ограничения.
     */
    public static function iniBytes(string $key): int
    {
        $value = trim((string) ini_get($key));
        if ($value === '' || $value === '0' || $value === '-1') {
            return PHP_INT_MAX;
        }

        $number = (int) $value;
        switch (strtoupper(substr($value, -1))) {
            case 'G':
                $number *= 1024;
                // no break
            case 'M':
                $number *= 1024;
                // no break
            case 'K':
                $number *= 1024;
        }

        return $number > 0 ? $number : PHP_INT_MAX;
    }
}
