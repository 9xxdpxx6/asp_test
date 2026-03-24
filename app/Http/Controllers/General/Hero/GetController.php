<?php

namespace App\Http\Controllers\General\Hero;

use App\Http\Controllers\Controller;
use App\Http\Resources\Hero\HeroPublicResource;
use App\Models\HeroSetting;

class GetController extends Controller
{
    public function __invoke()
    {
        $hero = HeroSetting::query()->firstOrFail();

        return new HeroPublicResource($hero);
    }
}
