<?php

namespace App\Service;

use App\Models\Post;
use App\Models\PostImage;
use App\Support\DataUrlImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostService
{
    public function store($data)
    {
        try {
            DB::beginTransaction();

            $htmlContent = $data['content'];

            // Используем DOMDocument для парсинга HTML
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $htmlContent = mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8');
            $dom->loadHTML('<?xml encoding="UTF-8">' . $htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();  // Очистить ошибки после загрузки
            $image = $dom->getElementsByTagName('img')->item(0);

            // Превью — копия первой картинки контента
            $filePath = null;
            if ($image) {
                $previewPath = $image->getAttribute('src');
                $filePath = DataUrlImage::isDataUrl($previewPath)
                    ? DataUrlImage::storePath($previewPath, 'images/post')
                    : DataUrlImage::copyFromStorageUrl($previewPath, 'images/post');
            }

            // base64-картинки контента сохраняем в файлы и подменяем src
            foreach ($dom->getElementsByTagName('img') as $img) {
                $src = $img->getAttribute('src');
                if (DataUrlImage::isDataUrl($src)) {
                    $imageUrl = DataUrlImage::store($src, 'images/posts');
                    if ($imageUrl !== null) {
                        $img->setAttribute('src', $imageUrl);
                    }
                }
            }

            Post::create([
                'title' => $data['title'],
                'preview_path' => $filePath,
                'slug' => $data['slug'],
                'content' => $dom->saveHTML(),
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    public function update($data, Post $post)
    {
        try {
            DB::beginTransaction();

            // Проверяем входные данные
            if (!isset($data['content'], $data['title'], $data['slug'])) {
                throw new \InvalidArgumentException('Missing required data keys: content, title, or slug');
            }

            $htmlContent = $data['content'];

            // Используем DOMDocument для парсинга HTML
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $htmlContent = mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8');
            $dom->loadHTML('<?xml encoding="UTF-8">' . $htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();  // Очистить ошибки после загрузки

            // Получаем существующий пост по ID
            $post = Post::findOrFail($post->id);

            // Удаляем старое превью-изображение из хранилища
            if ($post->preview_path) {
                Storage::disk('public')->delete($post->preview_path);
                $post->update(['preview_path' => null]);
            }

            $image = $dom->getElementsByTagName('img')->item(0);

            if ($image) {
                $previewPath = $image->getAttribute('src');
                // Превью — копия первой картинки: из base64 или из файла, уже лежащего в нашем storage.
                // Внешние ссылки не скачиваем (раньше file_get_contents() брал любой адрес/локальный файл с его расширением).
                $filePath = DataUrlImage::isDataUrl($previewPath)
                    ? DataUrlImage::storePath($previewPath, 'images/post')
                    : DataUrlImage::copyFromStorageUrl($previewPath, 'images/post');

                if ($filePath !== null) {
                    $post->update(['preview_path' => $filePath]);
                }
            }
            // Находим текущие изображения в описании
            $currentImages = [];
            $currentDom = new \DOMDocument();

            if ($post->content) {
                libxml_use_internal_errors(true);
                $currentDom->loadHTML($post->content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                libxml_clear_errors();

                $currentImageTags = $currentDom->getElementsByTagName('img');
                foreach ($currentImageTags as $currentImg) {
                    $currentImages[] = $currentImg->getAttribute('src');
                }
            }

            // Проверяем наличие изображений в новом контенте
            $newDom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $newDom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            $newImageTags = $newDom->getElementsByTagName('img');
            $newImages = [];

            foreach ($newImageTags as $newImg) {
                $newImages[] = $newImg->getAttribute('src');
            }
// Удаляем старые изображения и их ссылки только если они не присутствуют в новом контенте
            foreach ($currentImages as $oldImage) {
                // Проверяем, есть ли это изображение в новом контенте
                if (!in_array($oldImage, $newImages)) {
                    // Удаляем изображение с сервера
                    $this->deleteStorageUrl($oldImage);
                }
            }


            // Обработка новых изображений
            $images = $dom->getElementsByTagName('img');
            foreach ($images as $img) {
                $src = $img->getAttribute('src');

                if (DataUrlImage::isDataUrl($src)) {
                    $imageUrl = DataUrlImage::store($src, 'images/posts');
                    if ($imageUrl !== null) {
                        $img->setAttribute('src', $imageUrl);
                    }
                }
                $htmlContent = $dom->saveHTML();
            }

            // Обновляем основные данные поста
            $post->update([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'content' => $htmlContent,
                // Обнуляем превью-изображение на случай, если новое не будет загружено
            ]);

            // Обрабатываем изображение из контента


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    public function delete($post)
    {
        try {
            DB::beginTransaction();

            if ($post->preview_path) {
                Storage::disk('public')->delete($post->preview_path);
            }

            // Находим текущие изображения в описании
            $currentImages = [];
            $currentDom = new \DOMDocument();

            if ($post->content) {
                libxml_use_internal_errors(true);
                $currentDom->loadHTML($post->content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                libxml_clear_errors();

                $currentImageTags = $currentDom->getElementsByTagName('img');
                foreach ($currentImageTags as $currentImg) {
                    $currentImages[] = $currentImg->getAttribute('src');
                }
            }

// Удаляем старые изображения и их ссылки
            foreach ($currentImages as $oldImage) {
                // Проверяем, есть ли это изображение в новом контенте
                $this->deleteStorageUrl($oldImage);
            }
            $post->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    /**
     * Удаляет файл, только если URL указывает на наш storage (внешние ссылки и пути с ".." пропускаем).
     */
    protected function deleteStorageUrl(string $url): void
    {
        $prefix = url('storage/') . '/';
        if (!str_starts_with($url, $prefix)) {
            return;
        }

        $path = rawurldecode(substr($url, strlen($prefix)));
        if ($path !== '' && !str_contains($path, '..')) {
            Storage::disk('public')->delete($path);
        }
    }
}
