<?php

namespace App\Http\Controllers\Admin\Category;

use App\Models\Category;

class EditController extends BaseController
{
    public function __invoke(Category $category)
    {
        $category->load('blocks');
        return view('category.edit', compact('category'));
    }
}
