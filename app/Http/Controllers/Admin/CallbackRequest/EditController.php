<?php

namespace App\Http\Controllers\Admin\CallbackRequest;

use App\Models\CallbackRequest;

class EditController extends BaseController
{
    public function __invoke(CallbackRequest $callback)
    {
        return view('callback.edit', compact('callback'));
    }
}
