<?php

namespace Database\Seeders;

use App\Models\ReviewWidget;
use App\Support\ReviewWidgetCatalog;
use Illuminate\Database\Seeder;

class ReviewWidgetSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = ReviewWidgetCatalog::defaults();
        $activeSlugs = array_column($defaults, 'slug');

        ReviewWidget::query()
            ->whereNotIn('slug', $activeSlugs)
            ->update([
                'is_active' => false,
                'show_on_home' => false,
                'home_sort_order' => null,
            ]);

        foreach ($defaults as $index => $item) {
            $widget = ReviewWidget::query()->firstOrNew([
                'slug' => $item['slug'],
            ]);

            $isNew = !$widget->exists;

            $widget->title = $item['title'];
            $widget->description = $item['description'] ?? null;
            $widget->provider = $item['provider'];
            $widget->render_type = $item['render_type'];
            $widget->config = $item['config'] ?? [];
            $widget->is_active = (bool) ($item['is_active'] ?? true);

            if ($isNew) {
                $widget->show_on_home = (bool) ($item['show_on_home'] ?? false);
                $widget->home_sort_order = $item['home_sort_order'] ?? null;
            }

            $widget->save();
        }

        $activeHomeCount = ReviewWidget::query()
            ->where('is_active', true)
            ->where('show_on_home', true)
            ->count();

        if ($activeHomeCount < 1) {
            foreach (array_slice($defaults, 0, 2) as $index => $item) {
                ReviewWidget::query()
                    ->where('slug', $item['slug'])
                    ->update([
                        'show_on_home' => true,
                        'home_sort_order' => $index + 1,
                    ]);
            }
        }
    }
}
