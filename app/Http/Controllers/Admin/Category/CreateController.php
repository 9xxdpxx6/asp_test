<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateController
{
        public function __invoke()
        {
            return view('category.create');
        }
}
