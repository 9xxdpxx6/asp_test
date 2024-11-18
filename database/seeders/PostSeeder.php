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
    public function run(): void
    {
        // Создаем 10 постов
        Post::factory()
            ->hasImages(3) // Создаем по 3 изображения для каждого поста
            ->count(50)
            ->create();
    }
}
