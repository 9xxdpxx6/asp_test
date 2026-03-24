<?php

namespace App\Service;

use App\Models\HeroSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HeroSettingService
{
    public function update(array $data, ?UploadedFile $background = null): void
    {
        DB::transaction(function () use ($data, $background) {
            $hero = HeroSetting::query()->firstOrFail();

            $ctaButtons = collect($data['cta_buttons'] ?? [])
                ->map(function (array $b, int $i): array {
                    $v = (string) ($b['variant'] ?? '');
                    if ($v !== 'light' && $v !== 'outline-light') {
                        $v = ($i % 2 === 0) ? 'light' : 'outline-light';
                    }

                    return [
                        'label' => trim((string) ($b['label'] ?? '')),
                        'icon' => trim((string) ($b['icon'] ?? '')) ?: 'fas fa-check',
                        'href' => (string) ($b['href'] ?? ''),
                        'variant' => $v,
                    ];
                })
                ->values()
                ->all();

            $update = [
                'title' => $data['title'],
                'subtitle' => $data['subtitle'] ?? null,
                'buttons_align' => $data['buttons_align'],
                'buttons_direction' => $data['buttons_direction'],
                'cta_buttons' => $ctaButtons,
                'trust_items' => $data['trust_items'] ?? [],
            ];

            if ($background) {
                if ($hero->background_image_path) {
                    Storage::disk('public')->delete($hero->background_image_path);
                }
                $update['background_image_path'] = $background->store('hero', 'public');
            }

            $hero->update($update);
        });
    }

    public function deleteBackground(HeroSetting $hero): void
    {
        if ($hero->background_image_path) {
            Storage::disk('public')->delete($hero->background_image_path);
            $hero->update(['background_image_path' => null]);
        }
    }
}
