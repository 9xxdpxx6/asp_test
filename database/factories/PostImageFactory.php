<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostImage>
 */
class PostImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = PostImage::class;

    public function definition()
    {
        return [
            'post_id' => Post::get()->random()->id, // This will associate with a Post
            'image_path' => $this->faker->imageUrl(640, 480), // Example random image
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
