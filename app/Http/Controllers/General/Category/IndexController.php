<?php

namespace App\Http\Controllers\General\Category;

use App\Http\Controllers\Controller;
use App\Http\Filters\CategoryFilter;
use App\Http\Requests\Category\FilterRequest;
use App\Http\Resources\Category\CategoryMinResource;
use App\Models\Category;

class IndexController extends Controller
{
    public function __invoke(FilterRequest $request)
    {
        $data = $request->validated();
        $data['sort'] = $data['sort'] ?? 'default';

        $filter = app()->make(CategoryFilter::class, ['queryParams' => array_filter($data)]);

        $categories = CategoryMinResource::collection(Category::filter($filter)->paginate(30));

        return $categories;
    }
}
