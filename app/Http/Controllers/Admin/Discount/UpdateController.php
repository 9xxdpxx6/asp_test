<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Http\Requests\Discount\UpdateRequest;
use App\Models\Discount;


class UpdateController extends BaseController
{

    public function __invoke(UpdateRequest $request, Discount $discount)
    {
        $data = $request->validated();

        $this->service->update($data, $discount);

        return redirect()->route('discount.show', $discount->id);
    }
}
