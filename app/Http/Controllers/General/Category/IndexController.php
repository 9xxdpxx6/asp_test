<?php

namespace App\Http\Controllers\General\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __invoke()
    {
        $categories = CategoryResource::collection(Category::all());
        return $categories;
    }
}
