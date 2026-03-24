<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomeFeature\UpdateWhyChooseUsRequest;
use App\Models\HomeFeatureBlock;
use App\Models\HomeFeatureSection;
use App\Models\HomeSectionSetting;
use App\Service\HomeFeatureBlockService;

class WhyChooseUsController extends Controller
{
    public function __construct(private readonly HomeFeatureBlockService $service)
    {
    }

    public function index()
    {
        $setting = HomeSectionSetting::query()
            ->where('section', HomeFeatureSection::WHY_CHOOSE_US)
            ->firstOrFail();

        $blocks = HomeFeatureBlock::query()
            ->where('section', HomeFeatureSection::WHY_CHOOSE_US)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.why-choose-us', [
            'heading' => $setting->heading,
            'blocks' => $blocks,
        ]);
    }

    public function update(UpdateWhyChooseUsRequest $request)
    {
        $this->service->syncWhyChooseUs(
            $request->validated('heading'),
            $request->validated('blocks')
        );

        return redirect()
            ->route('admin.why-choose-us')
            ->with('success', 'Секция «Почему мы» на главной обновлена.');
    }
}
