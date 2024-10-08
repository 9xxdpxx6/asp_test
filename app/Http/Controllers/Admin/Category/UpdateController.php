<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function __invoke(Category $category)
    {
        $data = request()->validate([
            'name' => 'string',
            'description' => 'string',
            'price' => ['required', 'numeric', 'between:0,99999.99'],
            'duration' => 'integer',
        ]);
        $category->update($data);
        return redirect()->route('category.show', $category->id);
    }
}
