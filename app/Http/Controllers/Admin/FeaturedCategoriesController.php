<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class FeaturedCategoriesController extends Controller
{
    public function index()
    {
        $allCategories = Category::orderBy('name')->get();
        $featured = Category::whereNotNull('featured_order')
            ->orderBy('featured_order')
            ->get();

        return view('admin.featured-categories', compact('allCategories', 'featured'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'featured_ids' => 'nullable|array|max:3',
            'featured_ids.*' => 'exists:categories,id',
        ]);

        // Reset all featured_order
        Category::whereNotNull('featured_order')->update(['featured_order' => null]);

        // Set new featured categories with order
        $ids = $request->input('featured_ids', []);
        foreach ($ids as $order => $id) {
            Category::where('id', $id)->update(['featured_order' => $order + 1]);
        }

        return redirect()->route('admin.featured-categories')
            ->with('success', 'Категории на главной обновлены!');
    }
}
