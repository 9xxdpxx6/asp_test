<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class ShowController extends BaseController
{
    public function __invoke(Category $category)
    {
        $category->load('blocks');
        return view('category.show', compact('category'));
    }
}
