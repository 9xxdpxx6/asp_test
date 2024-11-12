<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Requests\Category\StoreRequest;


class StoreController extends BaseController
{
    public function __invoke(StoreRequest $request)
    {
        $data = $request->validated();

        $this->service->store($data);
        return redirect()->route('category.index');
    }
}


