<?php

namespace App\Http\Controllers\General\Discount;

use App\Http\Controllers\Controller;
use App\Http\Resources\Discount\DiscountResource;
use App\Models\Discount;

class GetController extends Controller
{
    public function __invoke(Discount $discount)
    {
        return new DiscountResource($discount);
    }
}
