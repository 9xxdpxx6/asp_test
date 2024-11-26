<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Models\Category;
use App\Models\Discount;

class EditController extends BaseController
{
    public function __invoke(Discount $discount)
    {
        return view('discount.edit', compact('discount'));
    }
}
