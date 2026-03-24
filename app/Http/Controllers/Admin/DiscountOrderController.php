<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscountOrderController extends Controller
{
    public function index()
    {
        $discounts = Discount::orderByRaw('CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.discount-order', compact('discounts'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'discount_ids' => 'required|array|min:1',
            'discount_ids.*' => 'integer|exists:discounts,id',
        ]);

        $sortedIds = array_values(array_unique(array_map('intval', $validated['discount_ids'])));

        DB::transaction(function () use ($sortedIds) {
            $allIds = Discount::orderByRaw('CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('id')
                ->all();

            $missingIds = array_values(array_diff($allIds, $sortedIds));
            $finalOrder = array_merge($sortedIds, $missingIds);

            foreach ($finalOrder as $index => $discountId) {
                Discount::whereKey($discountId)->update(['sort_order' => $index + 1]);
            }
        });

        return redirect()
            ->route('admin.discount-order')
            ->with('success', 'Порядок скидок для списка программ лояльности обновлён.');
    }
}
