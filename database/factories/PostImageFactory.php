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
        // Список реальных URL изображений
        $imageUrls = [
            'https://via.placeholder.com/300x200.png?text=Image+1',
            'https://via.placeholder.com/300x200.png?text=Image+2',
            'https://via.placeholder.com/300x200.png?text=Image+3',
            'https://via.placeholder.com/300x200.png?text=Image+4',
            'https://via.placeholder.com/300x200.png?text=Image+5',
        ];

        return [
            'post_id' => Post::factory(), // Связь с постом
            'image_path' => $this->faker->randomElement($imageUrls), // Случайный URL из списка
        ];
    }
}
