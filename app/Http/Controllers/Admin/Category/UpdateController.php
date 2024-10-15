<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreRequest;
use App\Http\Requests\Category\UpdateRequest;
use App\Models\Category;
use App\Service\CategoryService;
use Illuminate\Http\Request;

class UpdateController extends BaseController
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function __invoke(UpdateRequest $request, Category $category)
    {
        $data = $request->validated();

        $this->categoryService->update($data, $category);

        return redirect()->route('category.show', $category->id);
    }
}
