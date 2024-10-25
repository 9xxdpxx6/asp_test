<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Post::factory()
            ->count(100)
            ->create()
            ->each(function ($post) {
                // For each post, create 3 images
                PostImage::factory()->count(3)->create(['post_id' => $post->id]);
            });
    }
}
