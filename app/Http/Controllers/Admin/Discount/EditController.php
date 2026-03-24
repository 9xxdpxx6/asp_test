<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Models\Discount;

class EditController extends BaseController
{
    public function __invoke(Discount $discount)
    {
        $discount->load(['blocks' => fn ($q) => $q->orderBy('sort_order')]);

        return view('discount.edit', compact('discount'));
    }
}
