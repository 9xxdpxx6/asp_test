<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AboutPage\UpdateRequest;
use App\Models\AboutBlock;
use App\Models\AboutSetting;
use App\Service\AboutPageService;
use App\Support\HeroCtaLinkCatalog;

class AboutPageController extends Controller
{
    public function __construct(private readonly AboutPageService $service)
    {
    }

    public function index()
    {
        $settings = AboutSetting::query()->first() ?? new AboutSetting([
            'hero_title' => 'О нашей автошколе',
            'hero_subtitle' => 'Добро пожаловать в «Автошкола Политех»',
            'cta_title' => 'Готовим грамотных водителей',
            'cta_text' => '',
            'cta_button_text' => 'Записаться на обучение',
            'cta_icon' => 'fas fa-graduation-cap',
            'cta_href' => '/prices',
        ]);

        $blocks = AboutBlock::orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $heroCtaLinkOptions = HeroCtaLinkCatalog::options();

        return view('admin.about-page', compact('settings', 'blocks', 'heroCtaLinkOptions'));
    }

    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();

        $this->service->updateSettings([
            'hero_title' => $validated['hero_title'],
            'hero_subtitle' => $validated['hero_subtitle'] ?? null,
            'cta_title' => $validated['cta_title'],
            'cta_text' => $validated['cta_text'],
            'cta_button_text' => $validated['cta_button_text'],
            'cta_icon' => $validated['cta_icon'] ?? 'fas fa-graduation-cap',
            'cta_href' => $validated['cta_href'] ?? '/prices',
        ]);

        $this->service->syncBlocks($validated['blocks']);

        return redirect()
            ->route('admin.about-page')
            ->with('success', 'Страница «О нас» обновлена.');
    }
}
