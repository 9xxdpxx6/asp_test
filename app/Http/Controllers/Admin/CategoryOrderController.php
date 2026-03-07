<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryOrderController extends Controller
{
    public function index()
    {
        $categories = Category::orderByRaw('CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.category-order', compact('categories'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'integer|exists:categories,id',
        ]);

        $sortedIds = array_values(array_unique(array_map('intval', $validated['category_ids'])));

        DB::transaction(function () use ($sortedIds) {
            $allIds = Category::orderByRaw('CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('id')
                ->all();

            $missingIds = array_values(array_diff($allIds, $sortedIds));
            $finalOrder = array_merge($sortedIds, $missingIds);

            foreach ($finalOrder as $index => $categoryId) {
                Category::whereKey($categoryId)->update(['sort_order' => $index + 1]);
            }
        });

        return redirect()
            ->route('admin.category-order')
            ->with('success', 'Порядок категорий для страницы цен обновлен.');
    }
}
