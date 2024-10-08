<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function __invoke()
    {
        $data = request()->validate([
            'name' => 'string',
            'description' => 'string',
            'price' => ['required', 'numeric', 'between:0,99999.99'],
            'duration' => 'integer',
        ]);

        Category::create($data);
        return redirect()->route('category.index');
    }
}


