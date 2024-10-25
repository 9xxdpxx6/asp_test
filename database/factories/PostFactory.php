<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Post::class;

    public function definition()
    {
        $createdAt = $this->faker->dateTimeBetween('-1 years', 'now'); // Random date within the last year
        $updatedAt = $this->faker->dateTimeBetween($createdAt, 'now'); // Random date after creation

        return [
            'title' => $this->faker->sentence,
            'slug' => $this->faker->slug,
            'content' => $this->faker->paragraph,
            'preview_path' => $this->faker->imageUrl(640, 480), // Example image URL
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }
}
