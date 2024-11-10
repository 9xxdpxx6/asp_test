<?php

namespace App\Http\Controllers\Admin\Post;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeleteController extends BaseController
{
    public function __invoke(Post $post)
    {
        $this->service->delete($post);

        return response()->json(['message' => 'Post and related images deleted successfully']);
    }

}
