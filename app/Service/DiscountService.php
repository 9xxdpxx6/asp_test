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
                    $filePath = 'images/discount/' . $fileName;

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
            }

            // Обновляем данные скидки
            $discount->update([
                'name' => $data['name'],
                'preview_path' => null,
                'slug' => $data['slug'],
                'percentage' => $data['percentage'],
                'description' => $htmlContent,
            ]);

            // Обрабатываем изображения
            $image = $dom->getElementsByTagName('img')->item(0);

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
                    $filePath = 'images/discount/' . $fileName;

                    // Сохраняем новое изображение
                    Storage::disk('public')->put($filePath, $imageData);

                    // Обновляем путь в записи
                    $discount->update([
                        'preview_path' => $filePath,
                    ]);
                }
            }

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
            $discount->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }
}
