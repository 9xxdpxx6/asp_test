<?php

namespace App\Http\Controllers\General\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class GetController extends Controller
{
    public function __invoke(Category $category)
    {
        return new CategoryResource($category);
    }
}
