<?php

namespace App\Service;

use App\Models\Discount;
use App\Models\DiscountBlock;
use App\Support\HtmlEntityDecoder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DiscountService
{
    public function store(array $data): void
    {
        DB::beginTransaction();

        try {
            $previewPath = null;
            if (!empty($data['preview']) && $data['preview'] instanceof UploadedFile) {
                $previewPath = $this->uploadPreviewImage($data['preview']);
            }

            $discount = Discount::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'sort_order' => (Discount::max('sort_order') ?? 0) + 1,
                'excerpt' => $data['excerpt'] ?? null,
                'preview_path' => $previewPath,
                'percentage' => $data['percentage'] ?? null,
                'description' => null,
            ]);

            if (!empty($data['blocks'])) {
                $this->syncBlocks($discount, $data['blocks']);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('DiscountService store: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    public function update(array $data, Discount $discount): void
    {
        DB::beginTransaction();

        try {
            $discount = Discount::findOrFail($discount->id);

            $update = [
                'name' => $data['name'],
                'slug' => $data['slug'],
                'excerpt' => $data['excerpt'] ?? null,
                'percentage' => $data['percentage'] ?? null,
            ];

            if (!empty($data['preview']) && $data['preview'] instanceof UploadedFile) {
                if ($discount->preview_path) {
                    $this->deleteStoragePath($discount->preview_path);
                }
                $update['preview_path'] = $this->uploadPreviewImage($data['preview']);
            }

            $discount->update($update);

            if (isset($data['blocks'])) {
                $this->syncBlocks($discount, $data['blocks']);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('DiscountService update: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Порядок и состав скидок в блоке на главной.
     *
     * @param  array<int>  $orderedIds
     */
    public function syncHomeDisplay(array $orderedIds): void
    {
        $orderedIds = array_values(array_filter(array_map('intval', $orderedIds)));

        DB::transaction(function () use ($orderedIds) {
            Discount::query()->update([
                'show_on_home' => false,
                'home_sort_order' => null,
            ]);

            foreach ($orderedIds as $index => $id) {
                Discount::where('id', $id)->update([
                    'show_on_home' => true,
                    'home_sort_order' => $index + 1,
                ]);
            }
        });
    }

    public function delete(Discount $discount): void
    {
        DB::beginTransaction();

        try {
            foreach ($discount->blocks as $block) {
                $this->deleteBlockImages($block);
            }

            if ($discount->preview_path) {
                $this->deleteStoragePath($discount->preview_path);
            }

            if ($discount->description) {
                $this->deleteImagesFromHtml($discount->description);
            }

            $discount->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('DiscountService delete: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    protected function uploadPreviewImage(UploadedFile $file): string
    {
        $fileName = 'disc_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('images/discounts', $fileName, 'public');
    }

    protected function syncBlocks(Discount $discount, array $blocks): void
    {
        $keepIds = [];
        foreach ($blocks as $blockData) {
            if (!empty($blockData['id'])) {
                $keepIds[] = $blockData['id'];
            }
        }

        $removedBlocks = $discount->blocks()->whereNotIn('id', $keepIds)->get();
        foreach ($removedBlocks as $removedBlock) {
            $this->deleteBlockImages($removedBlock);
            $removedBlock->delete();
        }

        foreach ($blocks as $index => $blockData) {
            $content = is_string($blockData['content'] ?? null)
                ? json_decode($blockData['content'], true)
                : ($blockData['content'] ?? []);

            if (!is_array($content)) {
                $content = [];
            }

            if (in_array($blockData['type'], ['text', 'image_text'])) {
                $content = $this->processBlockHtmlImages($content, $blockData['type']);
            }

            if (in_array($blockData['type'], ['image', 'gallery'])) {
                $content = $this->processBlockFileImages($content, $blockData['type']);
            }

            if (!empty($blockData['id'])) {
                $block = DiscountBlock::find($blockData['id']);
                if ($block && $block->discount_id === $discount->id) {
                    $block->update([
                        'type' => $blockData['type'],
                        'content' => $content,
                        'sort_order' => $index,
                    ]);
                }
            } else {
                $discount->blocks()->create([
                    'type' => $blockData['type'],
                    'content' => $content,
                    'sort_order' => $index,
                ]);
            }
        }
    }

    protected function processBlockHtmlImages(array $content, string $type): array
    {
        foreach (['html'] as $field) {
            if (!empty($content[$field])) {
                $content[$field] = $this->processHtmlImages($content[$field]);
            }
        }

        return $content;
    }

    protected function processBlockFileImages(array $content, string $type): array
    {
        return $content;
    }

    protected function processHtmlImages(string $html): string
    {
        if (!class_exists(\DOMDocument::class)) {
            return HtmlEntityDecoder::decodeString($html);
        }

        $html = HtmlEntityDecoder::decodeString($html);
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
                $filePath = 'images/discount_blocks/' . $fileName;

                Storage::disk('public')->put($filePath, $imageData);

                $img->setAttribute('src', url('storage/' . $filePath));
                $hasChanges = true;
            }
        }

        return HtmlEntityDecoder::decodeString($hasChanges ? $dom->saveHTML() : $html);
    }

    protected function deleteBlockImages(DiscountBlock $block): void
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

    protected function deleteImagesFromHtml(string $html): void
    {
        if (!class_exists(\DOMDocument::class)) {
            return;
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');
        foreach ($images as $img) {
            $this->deleteStorageFile($img->getAttribute('src'));
        }
    }

    protected function deleteStorageFile(string $url): void
    {
        $path = str_replace(url('storage/'), '', $url);
        if ($path && $path !== $url) {
            Storage::disk('public')->delete($path);
        }
    }

    protected function deleteStoragePath(string $relativePath): void
    {
        if (Str::startsWith($relativePath, ['http://', 'https://', '/'])) {
            return;
        }

        Storage::disk('public')->delete(ltrim($relativePath, '/'));
    }
}
