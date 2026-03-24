<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Models\Discount;

class ShowController extends BaseController
{
    public function __invoke(Discount $discount)
    {
        $discount->load('blocks');

        return view('discount.show', compact('discount'));
    }
}
