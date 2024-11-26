<?php

namespace App\Http\Controllers\Admin\CallbackRequest;

use App\Models\CallbackRequest;

class ShowController extends BaseController
{
    public function __invoke(CallbackRequest $callback)
    {
        return view('callback.show',compact('callback'));
    }
}
