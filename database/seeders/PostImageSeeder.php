<?php

namespace Database\Seeders;

use App\Models\PostImage;
use Illuminate\Database\Seeder;

class PostImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Создаем 30 изображений для случайных постов
        PostImage::factory()
            ->count(30)
            ->create();
    }
}

