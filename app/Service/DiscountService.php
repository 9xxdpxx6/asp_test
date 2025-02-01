<?php

namespace App\Service;

use App\Models\Discount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DiscountService
{
    public function store($data)
    {
        try {
            DB::beginTransaction();
            $htmlContent = $data['description'];

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
                    $filePath = 'images/discounts/' . $fileName;

                    Storage::disk('public')->put($filePath, $imageData);

                }
            }
            $discount = Discount::create([
                'name' => $data['name'],
                'preview_path' => $filePath,
                'slug' => $data['slug'],
                'percentage' => $data['percentage'],
                'description' => $htmlContent, // Сохраняем исходный контент
            ]);
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
                    $filePath = 'images/discount/' . $fileName;

                    Storage::disk('public')->put($filePath, $imageData);

                    $imageUrl = url('storage/' . $filePath);

                    // Заменяем src в теге <img> на URL
                    $img->setAttribute('src', $imageUrl);
                }
                $updatedHtmlContent = $dom->saveHTML();
            }
            $discount->update(['description' => $updatedHtmlContent]);


            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    public function update($data, Discount $discount)
    {
        try {
            DB::beginTransaction();
            $htmlContent = $data['description'];

            // Используем DOMDocument для парсинга HTML
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();  // Очистить ошибки после загрузки

            // Получаем существующую запись
            $discount = Discount::findOrFail($discount->id);

            // Удаляем старый превью-изображение, если оно существует
            if ($discount->preview_path) {
                Storage::disk('public')->delete($discount->preview_path);
                $discount->update(['preview_path' => null]);
            }

            // Находим текущие изображения в описании
            $currentImages = [];
            $currentDom = new \DOMDocument();

            if ($discount->description) {
                libxml_use_internal_errors(true);
                $currentDom->loadHTML($discount->description, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
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
                    $filePath = 'images/discounts/' . $fileName;

                    Storage::disk('public')->put($filePath, $imageData);

                    // Обновляем путь к новому изображению в базе данных
                    $discount->update(['preview_path' => $filePath]);
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
                    $filePath = 'images/discount/' . $fileName;

                    Storage::disk('public')->put($filePath, $imageData);

                    $imageUrl = url('storage/' . $filePath);

                    // Заменяем src в теге <img> на URL
                    $img->setAttribute('src', $imageUrl);
                }
                $htmlContent = $dom->saveHTML();
            }
            // Обновляем данные скидки
            $discount->update([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'percentage' => $data['percentage'],
                'description' => $htmlContent,
            ]);

//            // Обрабатываем изображения
//            $image = $dom->getElementsByTagName('img')->item(0);
//
//            if ($image) {
//                $previewPath = $image->getAttribute('src');
//                if (preg_match('/^data:image\/(\w+);base64,/', $previewPath, $type)) {
//                    // Определяем расширение изображения
//                    $extension = strtolower($type[1]);
//
//                    // Убираем base64 и декодируем изображение
//                    $imageData = substr($previewPath, strpos($previewPath, ',') + 1);
//                    $imageData = base64_decode($imageData);
//
//                    // Генерируем уникальное имя файла
//                    $fileName = 'image_' . time() . '_' . Str::random(10) . '.' . $extension;
//                    $filePath = 'images/discount/' . $fileName;
//
//                    // Сохраняем новое изображение
//                    Storage::disk('public')->put($filePath, $imageData);
//
//                    // Обновляем путь в записи
//                    $discount->update([
//                        'preview_path' => $filePath,
//                    ]);
//                }
//            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    public function delete($discount)
    {
        try {
            DB::beginTransaction();
            if($discount->preview_path){
                Storage::disk('public')->delete($discount->preview_path);
            }
            // Находим текущие изображения в описании
            $currentImages = [];
            $currentDom = new \DOMDocument();

            if ($discount->description) {
                libxml_use_internal_errors(true);
                $currentDom->loadHTML($discount->description, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
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
            $discount->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }
}
