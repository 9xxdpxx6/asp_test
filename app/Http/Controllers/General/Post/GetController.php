<?php

namespace App\Http\Controllers\General\Post;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\PostMinResource;
use App\Models\Post;

class GetController extends Controller
{
    public function __invoke(Post $post)
    {
        return new PostMinResource($post);
    }
}
