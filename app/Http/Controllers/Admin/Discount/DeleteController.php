<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Discount;
use App\Service\CategoryService;
use Illuminate\Http\Request;

class DeleteController extends BaseController
{
    public function __invoke(Discount $discount)
    {
        $this->service->delete($discount);
        return redirect()->route('discount.index');
    }
}
