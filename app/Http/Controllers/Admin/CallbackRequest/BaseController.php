<?php

namespace App\Http\Controllers\Admin\CallbackRequest;

use App\Http\Controllers\Controller;
use App\Service\CallbackRequestService;

class BaseController extends Controller
{
    protected $service;

    public function __construct(CallbackRequestService $service)
    {
        $this->service = $service;
    }
}
