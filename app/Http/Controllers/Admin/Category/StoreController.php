<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreRequest;
use App\Models\Category;
use App\Service\CategoryService;
use Illuminate\Http\Request;

class StoreController extends BaseController
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function __invoke(StoreRequest $request)
    {
        $data = $request->validated();

        $this->categoryService->store(date);
        return redirect()->route('category.index');
    }
}


