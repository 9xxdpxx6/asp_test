<?php

namespace App\Http\Controllers\General\About;

use App\Http\Controllers\Controller;
use App\Http\Resources\About\AboutBlockResource;
use App\Models\AboutBlock;
use App\Models\AboutSetting;

class GetController extends Controller
{
    public function __invoke()
    {
        $settings = AboutSetting::query()->first();

        if (!$settings) {
            return response()->json([
                'settings' => null,
                'blocks' => [],
            ]);
        }

        $blocks = AboutBlock::orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'settings' => [
                'hero_title' => $settings->hero_title,
                'hero_subtitle' => $settings->hero_subtitle,
                'cta_title' => $settings->cta_title,
                'cta_text' => $settings->cta_text,
                'cta_button_text' => $settings->cta_button_text,
                'cta_icon' => $settings->cta_icon ?: 'fas fa-graduation-cap',
                'cta_href' => $settings->cta_href ?: '/prices',
            ],
            'blocks' => AboutBlockResource::collection($blocks)->resolve(),
        ]);
    }
}
