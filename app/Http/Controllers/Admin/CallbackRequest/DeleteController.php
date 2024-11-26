<?php

namespace App\Http\Controllers\Admin\CallbackRequest;

use App\Models\CallbackRequest;

class DeleteController extends BaseController
{
    public function __invoke(CallbackRequest $category)
    {
        $this->service->delete($category);
        return redirect()->route('callback.index');
    }
}
