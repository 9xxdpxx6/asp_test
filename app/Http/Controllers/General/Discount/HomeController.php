<?php

namespace App\Http\Controllers\General\Discount;

use App\Http\Controllers\Controller;
use App\Http\Resources\Discount\DiscountHomeResource;
use App\Models\Discount;

class HomeController extends Controller
{
    public function __invoke()
    {
        $discounts = Discount::query()
            ->where('show_on_home', true)
            ->orderBy('home_sort_order')
            ->orderBy('id')
            ->get();

        return DiscountHomeResource::collection($discounts);
    }
}
