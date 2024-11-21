<?php

namespace App\Http\Controllers\Admin\CallbackRequest;

use App\Http\Requests\CallbackRequest\StoreRequest;

class StoreController extends BaseController
{
    public function __invoke(StoreRequest $request)
    {
        $data = $request->validated();

        $this->service->store($data);
        return redirect()->route('callback.index');
    }
}


