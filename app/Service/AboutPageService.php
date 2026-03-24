<?php

namespace App\Service;

use App\Models\AboutBlock;
use App\Models\AboutSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AboutPageService
{
    public function updateSettings(array $data): void
    {
        AboutSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'hero_title' => $data['hero_title'],
                'hero_subtitle' => $data['hero_subtitle'] ?? null,
                'cta_title' => $data['cta_title'],
                'cta_text' => $data['cta_text'],
                'cta_button_text' => $data['cta_button_text'],
                'cta_icon' => $data['cta_icon'] ?? 'fas fa-graduation-cap',
                'cta_href' => \App\Support\HeroCtaLinkCatalog::normalize(
                    $data['cta_href'] ?? '/prices',
                    '/prices'
                ),
            ]
        );
    }

    public function syncBlocks(array $blocks): void
    {
        DB::transaction(function () use ($blocks) {
            $keepIds = [];
            $activeBlocks = array_values(array_filter($blocks, function (array $block) {
                return empty($block['pending_delete']);
            }));

            foreach ($activeBlocks as $index => $data) {
                $block = !empty($data['id']) ? AboutBlock::find($data['id']) : new AboutBlock();
                $currentImage = $block?->image;

                $imagePath = $currentImage;
                if (!empty($data['image']) && $data['image'] instanceof UploadedFile) {
                    $this->deleteManagedImage($currentImage);
                    $imagePath = $this->uploadImage($data['image']);
                } elseif (!empty($data['existing_image'])) {
                    $imagePath = $data['existing_image'];
                }

                $title = isset($data['title']) ? trim((string) $data['title']) : '';
                $title = $title === '' ? null : $title;

                $block->fill([
                    'title' => $title,
                    'description' => $data['description'],
                    'image' => $imagePath,
                    'sort_order' => $index + 1,
                    'image_on_left' => array_key_exists('image_on_left', $data)
                        ? filter_var($data['image_on_left'], FILTER_VALIDATE_BOOLEAN)
                        : true,
                ]);
                $block->save();

                $keepIds[] = $block->id;
            }

            $query = AboutBlock::query();
            if (!empty($keepIds)) {
                $query->whereNotIn('id', array_unique($keepIds));
            }

            $blocksToDelete = $query->get();

            foreach ($blocksToDelete as $block) {
                $this->deleteManagedImage($block->image);
                $block->delete();
            }
        });
    }

    protected function uploadImage(UploadedFile $file): string
    {
        $fileName = 'about_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $directory = public_path('storage/images/about');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $file->move($directory, $fileName);

        return 'images/about/' . $fileName;
    }

    protected function deleteManagedImage(?string $path): void
    {
        if (empty($path) || Str::startsWith($path, ['http://', 'https://', '/'])) {
            return;
        }

        $fullPath = public_path('storage/' . ltrim($path, '/'));

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
