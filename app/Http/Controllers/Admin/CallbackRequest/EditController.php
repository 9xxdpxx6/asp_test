<?php

namespace App\Http\Controllers\Admin\CallbackRequest;

use App\Models\Category;

class EditController extends BaseController
{
    public function __invoke(Category $category)
    {
        return view('category.edit', compact('category'));
    }
}
