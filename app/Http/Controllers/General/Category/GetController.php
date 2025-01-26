<?php

namespace App\Http\Controllers\General\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryMinResource;
use App\Models\Category;

class GetController extends Controller
{
    public function __invoke(Category $category)
    {
        return new CategoryMinResource($category);
    }
}
