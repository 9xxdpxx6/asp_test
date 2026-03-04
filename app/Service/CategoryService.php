<?php

namespace App\Service;

use App\Models\Category;
use App\Models\CategoryBlock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryService
{
    public function store($data)
    {
        try {
            DB::beginTransaction();

            $imagePath = null;
            if (!empty($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $imagePath = $this->uploadCategoryImage($data['image']);
            }

            $category = Category::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'icon' => $data['icon'] ?? null,
                'image' => $imagePath,
                'description' => $data['description'] ?? '',
                'price' => $data['price'],
                'duration' => $data['duration'] ?? null,
            ]);

            if (!empty($data['blocks'])) {
                $this->syncBlocks($category, $data['blocks']);
            }

            DB::commit();

            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CategoryService store error: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function update($data, Category $category)
    {
        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $data['name'],
                'slug' => $data['slug'],
                'icon' => $data['icon'] ?? $category->icon,
                'description' => $data['description'] ?? $category->description,
                'price' => $data['price'],
                'duration' => $data['duration'] ?? $category->duration,
            ];

            if (!empty($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                // Удаляем старое фото
                if ($category->image) {
                    $oldPath = public_path('storage/' . $category->image);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $updateData['image'] = $this->uploadCategoryImage($data['image']);
            }

            $category->update($updateData);

            if (isset($data['blocks'])) {
                $this->syncBlocks($category, $data['blocks']);
            }

            DB::commit();

            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CategoryService update error: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $data,
                'category_id' => $category->id ?? null
            ]);
            throw $e;
        }
    }

    public function delete($category)
    {
        try {
            DB::beginTransaction();

            // Удаляем изображения из блоков
            foreach ($category->blocks as $block) {
                $this->deleteBlockImages($block);
            }

            // Удаляем изображения из старого description (обратная совместимость)
            if ($category->description) {
                $this->deleteImagesFromHtml($category->description);
            }

            $category->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CategoryService delete error: ' . $e->getMessage(), [
                'exception' => $e,
                'category_id' => $category->id ?? null
            ]);
            throw $e;
        }
    }

    /**
     * Загружает фото категории и возвращает путь.
     */
    protected function uploadCategoryImage(\Illuminate\Http\UploadedFile $file): string
    {
        $fileName = 'cat_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $directory = public_path('storage/images/categories');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        $file->move($directory, $fileName);
        return 'images/categories/' . $fileName;
    }

    /**
     * Синхронизирует блоки категории: удаляет старые, создаёт новые.
     */
    protected function syncBlocks(Category $category, array $blocks)
    {
        // Собираем ID блоков, которые остаются
        $keepIds = [];
        foreach ($blocks as $blockData) {
            if (!empty($blockData['id'])) {
                $keepIds[] = $blockData['id'];
            }
        }

        // Удаляем блоки, которых нет в новом списке, и их изображения
        $removedBlocks = $category->blocks()->whereNotIn('id', $keepIds)->get();
        foreach ($removedBlocks as $removedBlock) {
            $this->deleteBlockImages($removedBlock);
            $removedBlock->delete();
        }

        // Создаём или обновляем блоки
        foreach ($blocks as $index => $blockData) {
            $content = is_string($blockData['content']) ? json_decode($blockData['content'], true) : $blockData['content'];

            // Обрабатываем изображения в HTML-контенте блоков типа text и image_text
            if (in_array($blockData['type'], ['text', 'image_text'])) {
                $content = $this->processBlockHtmlImages($content, $blockData['type']);
            }

            // Обрабатываем загрузку изображений для блоков типа image и gallery
            if (in_array($blockData['type'], ['image', 'gallery'])) {
                $content = $this->processBlockFileImages($content, $blockData['type']);
            }

            if (!empty($blockData['id'])) {
                // Обновляем существующий блок
                $block = CategoryBlock::find($blockData['id']);
                if ($block && $block->category_id === $category->id) {
                    $block->update([
                        'type' => $blockData['type'],
                        'content' => $content,
                        'sort_order' => $index,
                    ]);
                }
            } else {
                // Создаём новый блок
                $category->blocks()->create([
                    'type' => $blockData['type'],
                    'content' => $content,
                    'sort_order' => $index,
                ]);
            }
        }
    }

    /**
     * Обрабатывает base64-изображения в HTML-контенте блока.
     */
    protected function processBlockHtmlImages(array $content, string $type): array
    {
        $htmlFields = ['html'];
        
        foreach ($htmlFields as $field) {
            if (!empty($content[$field])) {
                $content[$field] = $this->processHtmlImages($content[$field]);
            }
        }

        return $content;
    }

    /**
     * Обрабатывает загруженные файлы изображений.
     */
    protected function processBlockFileImages(array $content, string $type): array
    {
        // Для блоков image и gallery URL-адреса уже подготовлены на клиенте
        return $content;
    }

    /**
     * Парсит HTML и заменяет base64-изображения на сохранённые файлы.
     */
    protected function processHtmlImages(string $html): string
    {
        if (!class_exists('DOMDocument')) {
            return $html;
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');
        $hasChanges = false;

        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            if (preg_match('/^data:image\/(\w+);base64,/', $src, $type)) {
                $extension = strtolower($type[1]);
                $imageData = substr($src, strpos($src, ',') + 1);
                $imageData = base64_decode($imageData);
                $fileName = 'image_' . time() . '_' . Str::random(10) . '.' . $extension;
                $filePath = 'images/categories/' . $fileName;

                $fullDirectoryPath = public_path('storage/images/categories');
                if (!is_dir($fullDirectoryPath)) {
                    mkdir($fullDirectoryPath, 0755, true);
                }

                file_put_contents(public_path('storage/' . $filePath), $imageData);

                $img->setAttribute('src', url('storage/' . $filePath));
                $hasChanges = true;
            }
        }

        return $hasChanges ? $dom->saveHTML() : $html;
    }

    /**
     * Удаляет изображения, связанные с блоком.
     */
    protected function deleteBlockImages(CategoryBlock $block)
    {
        $content = $block->content;

        switch ($block->type) {
            case 'text':
            case 'image_text':
                if (!empty($content['html'])) {
                    $this->deleteImagesFromHtml($content['html']);
                }
                if (!empty($content['image_url'])) {
                    $this->deleteStorageFile($content['image_url']);
                }
                break;

            case 'image':
                if (!empty($content['url'])) {
                    $this->deleteStorageFile($content['url']);
                }
                break;

            case 'gallery':
                if (!empty($content['images'])) {
                    foreach ($content['images'] as $image) {
                        if (!empty($image['url'])) {
                            $this->deleteStorageFile($image['url']);
                        }
                    }
                }
                break;
        }
    }

    /**
     * Удаляет изображения из HTML-контента.
     */
    protected function deleteImagesFromHtml(string $html)
    {
        if (!class_exists('DOMDocument')) {
            return;
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');
        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            $this->deleteStorageFile($src);
        }
    }

    /**
     * Удаляет файл из storage по URL.
     */
    protected function deleteStorageFile(string $url)
    {
        $path = str_replace(url('storage/'), '', $url);
        if ($path && $path !== $url) {
            $fullPath = public_path('storage/' . $path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }
}
