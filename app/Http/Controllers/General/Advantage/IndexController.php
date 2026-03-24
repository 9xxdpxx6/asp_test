<?php

namespace App\Http\Controllers\General\Advantage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Advantage\AdvantageResource;
use App\Models\Advantage;

class IndexController extends Controller
{
    public function __invoke()
    {
        $advantages = Advantage::orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return AdvantageResource::collection($advantages);
    }
}
