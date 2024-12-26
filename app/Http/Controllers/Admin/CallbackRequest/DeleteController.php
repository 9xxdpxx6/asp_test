<?php

namespace App\Http\Controllers\Admin\CallbackRequest;

use App\Models\CallbackRequest;

class DeleteController extends BaseController
{
    public function __invoke(CallbackRequest $callback)
    {
        $this->service->delete($callback);
        return redirect()->route('callback.index');
    }
}
