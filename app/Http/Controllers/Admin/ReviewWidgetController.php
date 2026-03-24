<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReviewWidget;
use App\Service\ReviewWidgetService;
use App\Support\ReviewWidgetCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class ReviewWidgetController extends Controller
{
    public function __construct(private readonly ReviewWidgetService $service)
    {
    }

    public function index()
    {
        $dbReady = Schema::hasTable('review_widgets');
        $activeSlugs = array_column(ReviewWidgetCatalog::defaults(), 'slug');

        if ($dbReady) {
            $onHome = ReviewWidget::query()
                ->where('is_active', true)
                ->whereIn('slug', $activeSlugs)
                ->where('show_on_home', true)
                ->orderByRaw('CASE WHEN home_sort_order IS NULL THEN 1 ELSE 0 END')
                ->orderBy('home_sort_order')
                ->orderBy('id')
                ->get();

            $all = ReviewWidget::query()
                ->where('is_active', true)
                ->whereIn('slug', $activeSlugs)
                ->orderBy('provider')
                ->orderBy('title')
                ->get();
        } else {
            $onHome = collect(ReviewWidgetCatalog::homeDefaults());
            $all = collect(ReviewWidgetCatalog::defaults());
        }

        return view('admin.reviews-builder', [
            'dbReady' => $dbReady,
            'onHome' => $onHome,
            'all' => $all,
        ]);
    }

    public function update(Request $request)
    {
        if (!Schema::hasTable('review_widgets')) {
            return redirect()
                ->route('admin.reviews-builder')
                ->with('error', 'Таблица review_widgets ещё не создана. Сначала выполните миграции.');
        }

        $request->validate([
            'widget_ids' => 'nullable|array',
            'widget_ids.*' => 'distinct|integer|exists:review_widgets,id',
        ]);

        $ids = array_values(array_map('intval', $request->input('widget_ids', [])));
        $activeSlugs = array_column(ReviewWidgetCatalog::defaults(), 'slug');

        $selected = ReviewWidget::query()
            ->where('is_active', true)
            ->whereIn('slug', $activeSlugs)
            ->whereIn('id', $ids)
            ->get(['id', 'provider']);

        if (count($ids) < 1) {
            throw ValidationException::withMessages([
                'widget_ids' => 'На главной должен быть хотя бы один виджет отзывов.',
            ]);
        }

        if (count($ids) > 2) {
            throw ValidationException::withMessages([
                'widget_ids' => 'На главной можно оставить не более двух виджетов.',
            ]);
        }

        if ($selected->count() !== count($ids)) {
            throw ValidationException::withMessages([
                'widget_ids' => 'Не удалось сохранить выбранные виджеты. Обновите страницу и попробуйте снова.',
            ]);
        }

        $providerCounts = $selected
            ->groupBy('provider')
            ->map(static fn ($items) => $items->count());

        if (($providerCounts->get('yandex', 0) > 1) || ($providerCounts->get('2gis', 0) > 1)) {
            throw ValidationException::withMessages([
                'widget_ids' => 'Можно выбрать максимум один виджет Яндекса и максимум один виджет 2ГИС.',
            ]);
        }

        $this->service->syncHomeDisplay($ids);

        return redirect()
            ->route('admin.reviews-builder')
            ->with('success', 'Блок отзывов на главной обновлён.');
    }
}
