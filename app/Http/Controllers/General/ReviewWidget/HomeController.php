<?php

namespace App\Http\Controllers\General\ReviewWidget;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewWidget\ReviewWidgetResource;
use App\Models\ReviewWidget;
use App\Support\ReviewWidgetCatalog;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function __invoke()
    {
        $activeSlugs = array_column(ReviewWidgetCatalog::defaults(), 'slug');

        if (!Schema::hasTable('review_widgets')) {
            return ReviewWidgetResource::collection(collect(ReviewWidgetCatalog::homeDefaults()));
        }

        $widgets = ReviewWidget::query()
            ->where('is_active', true)
            ->whereIn('slug', $activeSlugs)
            ->where('show_on_home', true)
            ->orderByRaw('CASE WHEN home_sort_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('home_sort_order')
            ->orderBy('id')
            ->get();

        if ($widgets->isEmpty()) {
            return ReviewWidgetResource::collection(collect(ReviewWidgetCatalog::homeDefaults()));
        }

        return ReviewWidgetResource::collection($widgets);
    }
}
