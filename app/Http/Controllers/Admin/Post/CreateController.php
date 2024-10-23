<?php

namespace App\Http\Controllers\Admin\Post;

use App\Http\Controllers\Admin\Category\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateController extends BaseController
{
    public function __invoke()
    {
        return view('category.create');
    }
}
