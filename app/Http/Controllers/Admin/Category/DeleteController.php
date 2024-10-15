<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Service\CategoryService;
use Illuminate\Http\Request;

class DeleteController extends BaseController
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function __invoke(Category $category)
    {
        $this->categoryService->delete($category);
        return redirect()->route('category.index');
    }
}
