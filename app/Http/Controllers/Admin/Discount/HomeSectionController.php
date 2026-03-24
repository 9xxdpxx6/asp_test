<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Service\DiscountService;
use Illuminate\Http\Request;

class HomeSectionController extends Controller
{
    public function __construct(private readonly DiscountService $service)
    {
    }

    public function index()
    {
        $onHome = Discount::query()
            ->where('show_on_home', true)
            ->orderBy('home_sort_order')
            ->orderBy('id')
            ->get();

        $all = Discount::query()
            ->orderByRaw('CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.discounts-home', compact('onHome', 'all'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'discount_ids' => 'nullable|array|max:3',
            'discount_ids.*' => 'distinct|integer|exists:discounts,id',
        ]);

        $ids = array_values(array_map('intval', $request->input('discount_ids', [])));

        $this->service->syncHomeDisplay($ids);

        return redirect()
            ->route('admin.discounts.home-section')
            ->with('success', 'Блок «Скидки и акции» на главной обновлён.');
    }
}
