<?php

namespace App\Http\Controllers\General\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryMinResource;
use App\Models\Category;

class FeaturedController extends Controller
{
    public function __invoke()
    {
        $featured = Category::whereNotNull('featured_order')
            ->orderBy('featured_order')
            ->get();

        // Fallback: if no featured categories set, return first 3
        if ($featured->isEmpty()) {
            $featured = Category::orderBy('id')->limit(3)->get();
        }

        return CategoryMinResource::collection($featured);
    }
}
