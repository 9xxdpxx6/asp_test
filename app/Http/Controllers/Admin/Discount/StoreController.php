<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreRequest;
use App\Models\Category;
use App\Service\CategoryService;
use Illuminate\Http\Request;

class StoreController extends BaseController
{
    public function __invoke(StoreRequest $request)
    {
        $data = $request->validated();

        $this->service->store($data);
        return redirect()->route('category.index');
    }
}


