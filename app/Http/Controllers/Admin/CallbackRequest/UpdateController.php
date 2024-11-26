<?php

namespace App\Http\Controllers\Admin\CallbackRequest;

use App\Http\Requests\CallbackRequest\UpdateRequest;
use App\Models\CallbackRequest;

class UpdateController extends BaseController
{
    public function __invoke(UpdateRequest $request, CallbackRequest $callback)
    {
        $data = $request->validated();

        $this->service->update($data, $callback);

        return redirect()->route('callback.show', $callback->id);
    }
}
