<?php

namespace App\Http\Controllers\General\HomeFeature;

use App\Http\Controllers\Controller;
use App\Models\HomeFeatureBlock;
use App\Models\HomeFeatureSection;
use App\Models\HomeSectionSetting;

class WhyChooseUsGetController extends Controller
{
    public function __invoke()
    {
        $setting = HomeSectionSetting::query()
            ->where('section', HomeFeatureSection::WHY_CHOOSE_US)
            ->firstOrFail();

        $blocks = HomeFeatureBlock::query()
            ->where('section', HomeFeatureSection::WHY_CHOOSE_US)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['title', 'description', 'icon']);

        return response()->json([
            'data' => [
                'heading' => $setting->heading,
                'blocks' => $blocks,
            ],
        ]);
    }
}
