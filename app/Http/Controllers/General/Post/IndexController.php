<?php

namespace App\Http\Controllers\General\Post;

use App\Http\Controllers\Controller;
use App\Http\Filters\PostFilter;
use App\Http\Requests\Post\FilterRequest;
use App\Http\Resources\Post\PostMinResource;
use App\Models\Post;

class IndexController extends Controller
{
    public function __invoke(FilterRequest $request)
    {
        $data = $request->validated();
        $data['sort'] = $data['sort'] ?? 'default';

        $filter = app()->make(PostFilter::class, ['queryParams' => array_filter($data)]);

        $posts = PostMinResource::collection(Post::filter($filter)->paginate(28));

        return $posts;
    }
}
