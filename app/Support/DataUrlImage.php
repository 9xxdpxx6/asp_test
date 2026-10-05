<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Сохраняет изображения, пришедшие в виде data:image/...;base64 (из редактора блоков / Quill), в public storage.
 */
class DataUrlImage
{
    private const EXTENSIONS = [
        'jpeg' => 'jpg',
        'jpg' => 'jpg',
        'png' => 'png',
        'webp' => 'webp',
        'gif' => 'gif',
        'heic' => 'heic',
        'heif' => 'heif',
    ];

    public static function isDataUrl(?string $value): bool
    {
        return is_string($value) && str_starts_with($value, 'data:image/');
    }

    /**
     * Возвращает публичный URL сохранённого файла или null, если строка не base64-картинка допустимого типа.
     */
    public static function store(string $dataUrl, string $directory, string $prefix = 'image'): ?string
    {
        $path = self::storePath($dataUrl, $directory, $prefix);

        return $path === null ? null : url('storage/' . $path);
    }

    /**
     * То же, что store(), но возвращает путь относительно диска public (для полей вроде preview_path).
     */
    public static function storePath(string $dataUrl, string $directory, string $prefix = 'image'): ?string
    {
        if (!preg_match('/^data:image\/([a-z0-9.+-]+);base64,/i', $dataUrl, $match)) {
            return null;
        }

        $extension = self::EXTENSIONS[strtolower($match[1])] ?? null;
        if ($extension === null) {
            return null;
        }

        $binary = base64_decode(substr($dataUrl, strlen($match[0])), true);
        if ($binary === false || $binary === '') {
            return null;
        }

        $path = self::newPath($directory, $prefix, $extension);
        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    /**
     * Копирует картинку, уже лежащую в нашем storage (src вида {APP_URL}/storage/... или /storage/...), под новым именем.
     * Внешние адреса и всё, что не похоже на картинку, игнорируются — возвращается null.
     */
    public static function copyFromStorageUrl(string $src, string $directory, string $prefix = 'image'): ?string
    {
        $path = parse_url($src, PHP_URL_PATH);
        if (!is_string($path) || !str_starts_with($path, '/storage/')) {
            return null;
        }

        $host = parse_url($src, PHP_URL_HOST);
        if ($host !== null && $host !== parse_url(url('/'), PHP_URL_HOST)) {
            return null;
        }

        $relative = rawurldecode(substr($path, strlen('/storage/')));
        $extension = self::EXTENSIONS[strtolower(pathinfo($relative, PATHINFO_EXTENSION))] ?? null;
        if ($extension === null || str_contains($relative, '..') || !Storage::disk('public')->exists($relative)) {
            return null;
        }

        $newPath = self::newPath($directory, $prefix, $extension);
        Storage::disk('public')->copy($relative, $newPath);

        return $newPath;
    }

    private static function newPath(string $directory, string $prefix, string $extension): string
    {
        return trim($directory, '/') . '/' . $prefix . '_' . time() . '_' . Str::random(10) . '.' . $extension;
    }
}
