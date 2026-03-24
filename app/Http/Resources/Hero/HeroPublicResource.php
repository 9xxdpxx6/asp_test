<?php

namespace App\Http\Resources\Hero;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class HeroPublicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $bg = $this->background_image_path
            ? Storage::url($this->background_image_path)
            : asset('images/slider/slide-1-cars.JPG');

        $buttons = collect($this->cta_buttons ?? [])
            ->values()
            ->map(function (array $b, int $i): array {
                $v = $b['variant'] ?? null;
                if ($v !== 'light' && $v !== 'outline-light') {
                    $v = ($i % 2 === 0) ? 'light' : 'outline-light';
                }

                return [
                    'label' => $b['label'] ?? '',
                    'icon' => $b['icon'] ?? '',
                    'href' => $b['href'] ?? '',
                    'variant' => $v,
                ];
            })
            ->all();

        return [
            'background_image_url' => $bg,
            'title' => $this->title,
            'subtitle' => $this->subtitle ?? '',
            'buttons_align' => (($a = $this->buttons_align ?? 'center') === 'around') ? 'between' : $a,
            'buttons_direction' => $this->buttons_direction ?? 'row',
            'cta_buttons' => $buttons,
            'trust_items' => $this->trust_items ?? [],
        ];
    }
}
