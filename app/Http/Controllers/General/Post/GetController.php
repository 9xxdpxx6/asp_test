<?php

namespace App\Http\Controllers\General\Post;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class GetController extends Controller
{
    public function __invoke(Post $post)
    {
        return new PostResource($post);
    }
}
