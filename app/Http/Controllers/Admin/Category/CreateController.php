<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CreateController extends BaseController
{
    public function __invoke()
    {
        return view('category.create');
    }
}
