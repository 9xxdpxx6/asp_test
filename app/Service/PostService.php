<?php

namespace App\Service;

use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

            $filePath = null;
            if ($image) {
                $previewPath = $image->getAttribute('src');
                if (preg_match('/^data:image\/(\w+);base64,/', $previewPath, $type)) {
                    // Определяем расширение изображения
                    $extension = strtolower($type[1]);
                    // Убираем base64 и декодируем изображение
                    $imageData = substr($previewPath, strpos($previewPath, ',') + 1);
                    $imageData = base64_decode($imageData);
                    // Генерируем уникальное имя файла
                    $fileName = 'image_' . time() . '_' . Str::random(10) . '.' . $extension;
                    $filePath = 'images/post/' . $fileName;

                    Storage::disk('public')->put($filePath, $imageData);

                }
            }
            $post = Post::create([
                'title' => $data['title'],
                'preview_path' => $filePath,
                'slug' => $data['slug'],
                'content' => "фывфывфывфыфывфывфывфыфывфывфывфыфывфывфывфыфывфывфывфы", // Сохраняем исходный контент
            ]);
            // Получаем все теги <img>
            $images = $dom->getElementsByTagName('img');
            if($images->length > 0){
                foreach ($images as $img) {
                    $src = $img->getAttribute('src');

                    if (preg_match('/^data:image\/(\w+);base64,/', $src, $type)) {
                        // Определяем расширение изображения
                        $extension = strtolower($type[1]);
                        // Убираем base64 и декодируем изображение
                        $imageData = substr($src, strpos($src, ',') + 1);
                        $imageData = base64_decode($imageData);
                        // Генерируем уникальное имя файла
                        $fileName = 'image_' . time() . '_' . Str::random(10) . '.' . $extension;
                        $filePath = 'images/posts/' . $fileName;

                        Storage::disk('public')->put($filePath, $imageData);

                        $imageUrl = url('storage/' . $filePath);

                        // Заменяем src в теге <img> на URL
                        $img->setAttribute('src', $imageUrl);
                    }
                    $updatedHtmlContent = $dom->saveHTML();
                }
                $post->update(['content' => $updatedHtmlContent]);
            }


            // Создаем пост перед обработкой изображений


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
                // Проверяем, является ли изображение base64
                if (preg_match('/^data:image\/(\w+);base64,/', $previewPath, $type)) {
                    $extension = strtolower($type[1]); // Определяем расширение изображения
                    $imageData = substr($previewPath, strpos($previewPath, ',') + 1); // Убираем мета-данные base64
                    $imageData = base64_decode($imageData); // Декодируем изображение

                    if ($imageData === false) {
                        throw new \Exception('Base64 image decoding failed');
                    }

                    // Генерируем уникальное имя файла и сохраняем в хранилище
                    $fileName = 'image_' . time() . '_' . Str::random(10) . '.' . $extension;
                    $filePath = 'images/post/' . $fileName;

                    Storage::disk('public')->put($filePath, $imageData);

                    // Обновляем путь к новому изображению в базе данных
                    $post->update(['preview_path' => $filePath]);
                } else {
                    //Получаем содержимое изображения по URL
                    $baseUrl = "http://127.0.0.1:8000/";
                    $modifiedUrl = str_replace($baseUrl, '', $previewPath);


                    $imageData = file_get_contents($modifiedUrl);
                    if ($imageData === false) {
                        throw new Exception('Failed to retrieve image from URL');
                    }

                    // Генерируем уникальное имя файла и сохраняем в хранилище
                    $extension = pathinfo($previewPath, PATHINFO_EXTENSION); // Получаем расширение из URL
                    $fileName = 'image_' . time() . '_' . Str::random(10) . '.' . $extension;
                    $filePath = 'images/post/' . $fileName;

                    // Сохраняем изображение в хранилище
                    Storage::disk('public')->put($filePath, $imageData);

                    // Обновляем путь к новому изображению в базе данных
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
                    $path = str_replace(url('storage/'), '', $oldImage);
                    Storage::disk('public')->delete($path);
                }
            }


            // Обработка новых изображений
            $images = $dom->getElementsByTagName('img');
            foreach ($images as $img) {
                $src = $img->getAttribute('src');

                if (preg_match('/^data:image\/(\w+);base64,/', $src, $type)) {
                    // Определяем расширение изображения
                    $extension = strtolower($type[1]);
                    // Убираем base64 и декодируем изображение
                    $imageData = substr($src, strpos($src, ',') + 1);
                    $imageData = base64_decode($imageData);
                    // Генерируем уникальное имя файла
                    $fileName = 'image_' . time() . '_' . Str::random(10) . '.' . $extension;
                    $filePath = 'images/posts/' . $fileName;

                    Storage::disk('public')->put($filePath, $imageData);

                    $imageUrl = url('storage/' . $filePath);

                    // Заменяем src в теге <img> на URL
                    $img->setAttribute('src', $imageUrl);
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
                $path = str_replace(url('storage/'), '', $oldImage);
                Storage::disk('public')->delete($path);
            }
            $post->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }
}
