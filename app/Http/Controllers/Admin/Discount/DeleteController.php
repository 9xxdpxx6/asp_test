<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Service\CategoryService;
use Illuminate\Http\Request;

class DeleteController extends BaseController
{
    public function __invoke(Category $category)
    {
        $this->service->delete($category);
        return redirect()->route('category.index');
    }
}
