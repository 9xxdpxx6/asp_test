<?php

namespace App\Http\Controllers\General\Contact;

use App\Http\Controllers\Controller;
use App\Http\Resources\Contact\ContactPagePublicResource;
use App\Models\ContactPageSetting;

class GetController extends Controller
{
    public function __invoke()
    {
        $settings = ContactPageSetting::query()->firstOrFail();

        return new ContactPagePublicResource($settings);
    }
}
