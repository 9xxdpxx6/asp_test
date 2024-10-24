<?php

namespace App\Service;

use App\Models\Post;
use Illuminate\Support\Facades\DB;

class PostService
{
    public function store($data)
    {
        $post = new Post();

        try {
            DB::beginTransaction();

            $post = Post::create($data);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    public function update($data,Post $post)
    {

        try {
            DB::beginTransaction();

            $post = Post::update($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }

    public function delete($data)
    {
        try {
            DB::beginTransaction();
            $data->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500);
        }
    }
}
