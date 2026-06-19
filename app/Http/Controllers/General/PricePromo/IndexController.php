<?php

namespace App\Http\Controllers\General\PricePromo;

use App\Http\Controllers\Controller;
use App\Http\Resources\PricePromo\PricePromoResource;
use App\Models\PricePromo;

class IndexController extends Controller
{
    public function __invoke()
    {
        $promos = PricePromo::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'data' => PricePromoResource::collection($promos)->resolve(),
        ]);
    }
}
