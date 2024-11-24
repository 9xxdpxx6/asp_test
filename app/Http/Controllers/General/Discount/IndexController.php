<?php

namespace App\Http\Controllers\General\Discount;

use App\Http\Controllers\Controller;
use App\Http\Filters\DiscountFilter;
use App\Http\Requests\Post\FilterRequest;
use App\Http\Resources\Discount\DiscountResource;
use App\Http\Resources\Post\PostResource;
use App\Models\Discount;

class IndexController extends Controller
{
    public function __invoke()
    {
        $data['sort'] = $data['sort'] ?? 'default';

        $filter = app()->make(DiscountFilter::class, ['queryParams' => array_filter($data)]);

        $discounts = DiscountResource::collection(Discount::filter($filter)->paginate(30));

        return $discounts;
    }
}
