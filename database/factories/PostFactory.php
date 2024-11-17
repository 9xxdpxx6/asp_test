<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $title = $this->faker->sentence;
        $imageUrls = [
            'https://via.placeholder.com/300x200.png?text=Image+1',
            'https://via.placeholder.com/300x200.png?text=Image+2',
            'https://via.placeholder.com/300x200.png?text=Image+3',
            'https://via.placeholder.com/300x200.png?text=Image+4',
            'https://via.placeholder.com/300x200.png?text=Image+5',
        ];
        return [
            'title' => $title,
            'preview_path' => $this->faker->randomElement($imageUrls), // Можно добавить путь к реальному изображению
            'slug' => Str::slug($title),
            'content' => $this->faker->paragraphs(3, true),
        ];
    }
}
