<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Http\Filters\DiscountFilter;
use App\Http\Requests\Discount\FilterRequest;
use App\Models\Discount;

class IndexController extends BaseController
{
    public function __invoke(FilterRequest $request)
    {
        $data = $request->validated();
        $data['sort'] = $data['sort'] ?? 'default';

        $filter = app()->make(DiscountFilter::class, ['queryParams' => array_filter($data)]);

        $discounts = Discount::filter($filter)->paginate(30);

        return view('discount.index',compact('discounts'));
    }
}
