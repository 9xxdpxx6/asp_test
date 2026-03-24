<?php

namespace App\Http\Controllers\General\Discount;

use App\Http\Controllers\Controller;
use App\Http\Filters\DiscountFilter;
use App\Http\Resources\Discount\DiscountMinResource;
use App\Models\Discount;

class IndexController extends Controller
{
    public function __invoke()
    {
        $data = ['sort' => request('sort', 'default')];

        $filter = app()->make(DiscountFilter::class, ['queryParams' => array_filter($data)]);

        $discounts = DiscountMinResource::collection(Discount::filter($filter)->paginate(30));

        return $discounts;
    }
}
