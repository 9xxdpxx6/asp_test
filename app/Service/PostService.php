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
                    $filePath = 'post/images/' . $fileName;

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

//            $images = $dom->getElementsByTagName('img');
//            foreach ($images as $image) {
//                $src = $image->getAttribute('src');
//
//                // Проверяем, является ли src изображением в формате base64
//                if (preg_match('/^data:image\/(\w+);base64,/', $src, $type)) {
//                    // Определяем расширение изображения
//                    $extension = strtolower($type[1]);
//                    // Убираем base64 и декодируем изображение
//                    $imageData = substr($src, strpos($src, ',') + 1);
//                    $imageData = base64_decode($imageData);
//
//                    // Генерируем уникальное имя файла
//                    $fileName = 'image_' . time() . '_' . Str::random(10) . '.' . $extension;
//                    $filePath = 'post/images/' . $fileName;
//
//                    // Сохраняем изображение в файловой системе
//                    Storage::disk('public')->put($filePath, $imageData);
//
//                    // Заменяем base64 изображение на URL загруженного файла
//                    $image->setAttribute('src', Storage::url($filePath));
//
//                    // Сохраняем информацию об изображении в таблице post_images
//                    PostImage::create([
//                        'post_id' => $post->id,
//                        'image_path' => $filePath,
//                    ]);
//                }
//            }
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
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

            // Удаляем старый превью-изображение
            if ($post->preview_path) {
                Storage::disk('public')->delete($post->preview_path);
            }

            // Обновляем данные поста
            $post->update([
                'title' => $data['title'],
                'preview_path' => null,
                'slug' => $data['slug'],
                'content' => $htmlContent,
            ]);

            // Обрабатываем изображения
            $image = $dom->getElementsByTagName('img')->item(0);
            if ($image) {
                $previewPath = $image->getAttribute('src');
                if (preg_match('/^data:image\/(\w+);base64,/', $previewPath, $type)) {
                    $extension = strtolower($type[1]);
                    $fileName = 'image_' . time() . '_' . Str::random(10) . '.' . $extension;
                    $filePath = 'post/images/' . $fileName;

                    $post->update(['preview_path' => $filePath]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage()); // Выводим сообщение об ошибке для диагностики
            abort(500);
        }
    }


    public function delete($post)
    {
        try {
            DB::beginTransaction();
//            if($post->preview_path){
//                $oldImages = PostImage::where('post_id', $post->id)->get();
//
//                if ($oldImages->isNotEmpty()) { // Проверяем, есть ли изображения
//                    foreach ($oldImages as $oldImage) {
//                        // Удаляем файл изображения с диска
//                        Storage::disk('public')->delete($oldImage->image_path);
//
//                        // Удаляем запись из базы данных
//                        $oldImage->delete();
//                    }
//                }
//
//                // Удаляем сам пост из базы данных
//                $post->delete();
//            }else{
//                $post->delete();
//            }
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
