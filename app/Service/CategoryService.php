<?php

namespace App\Service;

use App\Models\Category;
use App\Models\CategoryImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;

class CategoryService
{
    public function store($data)
    {
//        $category = new Category();

        try {
            DB::beginTransaction();

            // Получаем HTML-контент из поля content
            $htmlContent = $data['description'];

            // Используем DOMDocument для парсинга HTML
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();  // Очистить ошибки после загрузки

            $category = Category::create([
                'name' => $data['name'],
                'description' => $htmlContent,
                'price' => $data['price'],
                'duration' => $data['duration'],
            ]);

            $images = $dom->getElementsByTagName('img');
            foreach ($images as $image) {
                $src = $image->getAttribute('src');

                // Проверяем, является ли src изображением в формате base64
                if (preg_match('/^data:image\/(\w+);base64,/', $src, $type)) {
                    // Определяем расширение изображения
                    $extension = strtolower($type[1]);
                    // Убираем base64 и декодируем изображение
                    $imageData = substr($src, strpos($src, ',') + 1);
                    $imageData = base64_decode($imageData);

                    // Генерируем уникальное имя файла
                    $fileName = 'image_' . time() . '_' . Str::random(10) . '.' . $extension;
                    $filePath = 'uploads/images/' . $fileName;

                    // Сохраняем изображение в файловой системе
                    Storage::disk('public')->put($filePath, $imageData);

                    // Заменяем base64 изображение на URL загруженного файла
                    $image->setAttribute('src', Storage::url($filePath));

                    // Сохраняем информацию об изображении в таблице post_images
                    CategoryImage::create([
                        'category_id' => $category->id,
                        'image_path' => $filePath,
                    ]);
                }
            }
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    public function update($data,Category $category)
    {

        try {
            DB::beginTransaction();
            $htmlContent = $data['description'];

            // Используем DOMDocument для парсинга HTML
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();  // Очистить ошибки после загрузки
            $category = Category::findOrFail($category->id);

            $oldImages = CategoryImage::where('category_id', $category->id)->get();

            // Удаляем старые изображения из файловой системы и базы данных
            foreach ($oldImages as $oldImage) {
                // Удаляем файл изображения с диска
                Storage::disk('public')->delete($oldImage->image_path);

                // Удаляем запись из базы данных
                $oldImage->delete();
            }
            $category->update([
                'name' => $data['name'],
                'description' => $htmlContent,
                'price' => $data['price'],
                'duration' => $data['duration'],
            ]);
            $images = $dom->getElementsByTagName('img');
            foreach ($images as $image) {
                $src = $image->getAttribute('src');

                // Проверяем, является ли src изображением в формате base64
                if (preg_match('/^data:image\/(\w+);base64,/', $src, $type)) {
                    // Определяем расширение изображения
                    $extension = strtolower($type[1]);
                    // Убираем base64 и декодируем изображение
                    $imageData = substr($src, strpos($src, ',') + 1);
                    $imageData = base64_decode($imageData);

                    // Генерируем уникальное имя файла
                    $fileName = 'image_' . time() . '_' . Str::random(10) . '.' . $extension;
                    $filePath = 'uploads/images/' . $fileName;

                    // Сохраняем изображение в файловой системе
                    Storage::disk('public')->put($filePath, $imageData);

                    // Заменяем base64 изображение на URL загруженного файла
                    $image->setAttribute('src', Storage::url($filePath));

                    // Сохраняем информацию об изображении в таблице post_images
                    CategoryImage::create([
                        'category_id' => $category->id,
                        'image_path' => $filePath,
                    ]);
                }
            }
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
            $oldImages = CategoryImage::where('category_id', $category->id)->get();

            foreach ($oldImages as $oldImage) {
                Storage::disk('public')->delete($oldImage->image_path);

                $oldImage->delete();
            }

            $category->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }
}
