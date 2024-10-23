<?php

namespace App\Http\Controllers\Admin\Post;

use App\Http\Controllers\Admin\Category\BaseController;
use App\Models\Category;

class EditController extends BaseController
{
    public function __invoke(Category $category)
    {
        return view('category.edit', compact('category'));
    }
}
