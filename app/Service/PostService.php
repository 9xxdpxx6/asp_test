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
            $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
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

            // Создаем пост перед обработкой изображений
            $post = Post::create([
                'title' => $data['title'],
                'preview_path' => $filePath,
                'slug' => $data['slug'],
                'content' => $htmlContent, // Сохраняем исходный контент
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
            $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            // Получаем существующий пост по ID
            $post = Post::findOrFail($post->id);

            // Удаляем старое превью-изображение из хранилища
            if ($post->preview_path) {
                Storage::disk('public')->delete($post->preview_path);
            }

            // Обновляем основные данные поста
            $post->update([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'content' => $htmlContent,
                'preview_path' => null, // Обнуляем превью-изображение на случай, если новое не будет загружено
            ]);

            // Обрабатываем изображение из контента
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
                }
            }

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

            if($post->preview_path){
                Storage::disk('public')->delete($post->preview_path);
            }
            $post->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }
}
