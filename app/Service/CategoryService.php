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

        try {
            DB::beginTransaction();

            // Получаем HTML-контент из поля content
            $htmlContent = $data['description'];

            // Используем DOMDocument для парсинга HTML
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();  // Очистить ошибки после загрузки

            $image = $dom->getElementsByTagName('img')->item(0);

            $category = Category::create([
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

    public function update($data,Category $category)
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

            if($category->icon){
                Storage::disk('public')->delete($category->icon);
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

            if($category->preview_path){
                Storage::disk('public')->delete($category->preview_path);
            }
            $category->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }
}
