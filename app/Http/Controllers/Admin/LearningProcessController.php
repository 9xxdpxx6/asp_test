<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomeFeature\UpdateLearningProcessRequest;
use App\Models\HomeFeatureBlock;
use App\Models\HomeFeatureSection;
use App\Models\HomeSectionSetting;
use App\Service\HomeFeatureBlockService;

class LearningProcessController extends Controller
{
    public function __construct(private readonly HomeFeatureBlockService $service)
    {
    }

    public function index()
    {
        $setting = HomeSectionSetting::query()
            ->where('section', HomeFeatureSection::LEARNING_PROCESS)
            ->firstOrFail();

        $blocks = HomeFeatureBlock::query()
            ->where('section', HomeFeatureSection::LEARNING_PROCESS)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.learning-process', [
            'heading' => $setting->heading,
            'subheading' => $setting->subheading,
            'blocks' => $blocks,
        ]);
    }

    public function update(UpdateLearningProcessRequest $request)
    {
        $validated = $request->validated();
        $this->service->syncLearningProcess(
            $validated['heading'],
            $validated['subheading'],
            $validated['blocks']
        );

        return redirect()
            ->route('admin.learning-process')
            ->with('success', 'Секция «Как проходит обучение» на главной обновлена.');
    }
}
