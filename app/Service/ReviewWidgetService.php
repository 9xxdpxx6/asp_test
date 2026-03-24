<?php

namespace App\Service;

use App\Models\ReviewWidget;
use Illuminate\Support\Facades\DB;

class ReviewWidgetService
{
    /**
     * @param  array<int>  $orderedIds
     */
    public function syncHomeDisplay(array $orderedIds): void
    {
        $orderedIds = array_values(array_unique(array_map('intval', $orderedIds)));

        DB::transaction(function () use ($orderedIds) {
            ReviewWidget::query()->update([
                'show_on_home' => false,
                'home_sort_order' => null,
            ]);

            foreach ($orderedIds as $index => $id) {
                ReviewWidget::query()
                    ->where('id', $id)
                    ->where('is_active', true)
                    ->update([
                        'show_on_home' => true,
                        'home_sort_order' => $index + 1,
                    ]);
            }
        });
    }
}
