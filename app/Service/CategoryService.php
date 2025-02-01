<?php

namespace App\Service;

use App\Models\Category;
use App\Models\CategoryImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryService
{
    public function store($data)
    {
        try {
            DB::beginTransaction();
            // Получаем HTML-контент из поля content
            $htmlContent = $data['description'];
            // Используем DOMDocument для парсинга HTML
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $htmlContent = mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8');
            $dom->loadHTML('<?xml encoding="UTF-8">' . $htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();  // Очистить ошибки после загрузки
            // Получаем все теги <img>
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
                    $filePath = 'images/categories/' . $fileName;

                    Storage::disk('public')->put($filePath, $imageData);

                    $imageUrl = url('storage/' . $filePath);

                    // Заменяем src в теге <img> на URL
                    $img->setAttribute('src', $imageUrl);
                }
                $updatedHtmlContent = $dom->saveHTML();
            }

            $category = Category::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'icon' => $data['icon'],
                'description' => $updatedHtmlContent,
                'price' => $data['price'],
                'duration' => $data['duration'],
            ]);

            DB::commit();


        } catch
        (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    public function update($data, Category $category)
    {

        try {
            DB::beginTransaction();

            if (!isset($data['description'], $data['name'], $data['slug'])) {
                throw new \InvalidArgumentException('Missing required data keys: description, title, or slug');
            }

            $htmlContent = $data['description'];
            // Используем DOMDocument для парсинга HTML
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();  // Очистить ошибки после загрузки
            $category = Category::findOrFail($category->id);

            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }
            // Находим текущие изображения в описании
            $currentImages = [];
            $currentDom = new \DOMDocument();

            if ($category->description) {
                libxml_use_internal_errors(true);
                $currentDom->loadHTML($category->description, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
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
                    $filePath = 'images/categories/' . $fileName;

                    Storage::disk('public')->put($filePath, $imageData);

                    $imageUrl = url('storage/' . $filePath);

                    // Заменяем src в теге <img> на URL
                    $img->setAttribute('src', $imageUrl);
                }
                $htmlContent = $dom->saveHTML();
            }

            $category->update([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'icon' => $data['icon'],
                'description' => $htmlContent,
                'price' => $data['price'],
                'duration' => $data['duration'],
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    public function delete($category)
    {
        try {
            DB::beginTransaction();

            if ($category->preview_path) {
                Storage::disk('public')->delete($category->preview_path);
            }
            // Находим текущие изображения в описании
            $currentImages = [];
            $currentDom = new \DOMDocument();

            if ($category->description) {
                libxml_use_internal_errors(true);
                $currentDom->loadHTML($category->description, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
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
            $category->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }


}
