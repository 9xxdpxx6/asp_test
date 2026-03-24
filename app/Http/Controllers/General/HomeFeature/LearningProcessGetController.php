<?php

namespace App\Http\Controllers\General\HomeFeature;

use App\Http\Controllers\Controller;
use App\Models\HomeFeatureBlock;
use App\Models\HomeFeatureSection;
use App\Models\HomeSectionSetting;

class LearningProcessGetController extends Controller
{
    public function __invoke()
    {
        $setting = HomeSectionSetting::query()
            ->where('section', HomeFeatureSection::LEARNING_PROCESS)
            ->firstOrFail();

        $blocks = HomeFeatureBlock::query()
            ->where('section', HomeFeatureSection::LEARNING_PROCESS)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['title', 'description', 'icon']);

        return response()->json([
            'data' => [
                'heading' => $setting->heading,
                'subheading' => $setting->subheading,
                'blocks' => $blocks,
            ],
        ]);
    }
}
