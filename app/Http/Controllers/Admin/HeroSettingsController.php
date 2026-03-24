<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSetting;
use App\Service\HeroSettingService;
use App\Support\HeroCtaLinkCatalog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HeroSettingsController extends Controller
{
    public function __construct(private readonly HeroSettingService $service)
    {
    }

    public function index()
    {
        $hero = HeroSetting::query()->firstOrFail();

        $raw = old('cta_buttons', $hero->cta_buttons ?? null);
        if (! is_array($raw)) {
            $raw = [];
        }
        $raw = array_values(array_filter($raw, fn ($r) => is_array($r)));
        $ctaRows = $this->normalizeCtaFormRows($raw);

        return view('admin.hero-settings', [
            'hero' => $hero,
            'heroCtaLinkOptions' => HeroCtaLinkCatalog::options(),
            'ctaRows' => $ctaRows,
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array{label: string, icon: string, href: string, variant: string}>
     */
    private function normalizeCtaFormRows(array $rows): array
    {
        if (count($rows) === 0) {
            $rows = [
                ['label' => 'Узнать стоимость', 'icon' => 'fas fa-graduation-cap', 'href' => '/prices', 'variant' => 'light'],
                ['label' => 'Записаться', 'icon' => 'fas fa-phone', 'href' => '#pricing-preview', 'variant' => 'outline-light'],
            ];
        }

        return collect($rows)
            ->take(3)
            ->map(function (array $row, int $i): array {
                $href = (string) ($row['href'] ?? '');
                $fallback = $i === 0 ? '/prices' : '#pricing-preview';
                $v = (string) ($row['variant'] ?? '');
                if ($v !== 'light' && $v !== 'outline-light') {
                    $v = ($i % 2 === 0) ? 'light' : 'outline-light';
                }

                return [
                    'label' => (string) ($row['label'] ?? ''),
                    'icon' => (string) ($row['icon'] ?? 'fas fa-check'),
                    'href' => HeroCtaLinkCatalog::normalize($href, $fallback),
                    'variant' => $v,
                ];
            })
            ->values()
            ->all();
    }

    public function update(Request $request)
    {
        $allowedHref = HeroCtaLinkCatalog::allowedPaths();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:5000',
            'buttons_align' => 'required|in:center,start,end,between',
            'buttons_direction' => 'required|in:row,column',
            'cta_buttons' => 'required|array|min:1|max:3',
            'cta_buttons.*.label' => 'required|string|max:120',
            'cta_buttons.*.icon' => 'nullable|string|max:120',
            'cta_buttons.*.href' => ['required', 'string', 'max:500', Rule::in($allowedHref)],
            'cta_buttons.*.variant' => 'required|in:light,outline-light',
            'trust_items' => 'nullable|array|max:8',
            'trust_items.*.icon' => 'nullable|string|max:120',
            'trust_items.*.value' => 'nullable|string|max:80',
            'trust_items.*.label' => 'nullable|string|max:255',
            'background_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:8192',
            'remove_background' => 'sometimes|boolean',
        ]);

        $hero = HeroSetting::query()->firstOrFail();

        if ($request->boolean('remove_background')) {
            $this->service->deleteBackground($hero);
            $hero->refresh();
        }

        $trustItems = collect($validated['trust_items'] ?? [])
            ->filter(fn ($row) => is_array($row) && ! empty(trim((string) ($row['label'] ?? ''))))
            ->values()
            ->map(fn ($row) => [
                'icon' => trim((string) ($row['icon'] ?? '')) ?: 'fas fa-check',
                'value' => trim((string) ($row['value'] ?? '')),
                'label' => trim((string) ($row['label'] ?? '')),
            ])
            ->all();

        $validated['trust_items'] = $trustItems;

        unset($validated['remove_background'], $validated['background_image']);

        $this->service->update($validated, $request->file('background_image'));

        return redirect()
            ->route('admin.hero-settings')
            ->with('success', 'Настройки Hero на главной обновлены.');
    }
}
