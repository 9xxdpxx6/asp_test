<?php

namespace App\Http\Controllers\Admin\CallbackRequest;

use App\Http\Filters\CallbackRequestFilter;
use App\Http\Requests\CallbackRequest\FilterRequest;
use App\Models\CallbackRequest;

class IndexController extends BaseController
{
    public function __invoke(FilterRequest $request)
    {
        $data = $request->validated();
        $data['sort'] = $data['sort'] ?? 'default';

        $filter = app()->make(CallbackRequestFilter::class, ['queryParams' => array_filter($data)]);

        $callbacks = CallbackRequest::filter($filter)->paginate(30);

        return view('callback.index',compact('callbacks'));

    }
}
