<?php

namespace App\Http\Controllers\Admin\CallbackRequest;

use App\Models\CallbackRequest;
use App\Models\Status;

class EditController extends BaseController
{
    public function __invoke(CallbackRequest $callback)
    {
        $statuses = Status::all();
        return view('callback.edit', compact('callback', 'statuses'));
    }
}
