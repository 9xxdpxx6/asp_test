<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Http\Controllers\Controller;
use App\Service\DiscountService;

class BaseController extends Controller
{
    protected $service;

    public function __construct(DiscountService $service)
    {
        $this->service = $service;
    }
}
