<?php

namespace App\Http\Controllers\General\Footer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Footer\FooterPublicResource;
use App\Models\FooterSetting;

class GetController extends Controller
{
    public function __invoke()
    {
        $footer = FooterSetting::query()->with('documents')->firstOrFail();

        return new FooterPublicResource($footer);
    }
}
