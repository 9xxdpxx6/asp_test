<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Yoga Class',
                'description' => 'Relax and rejuvenate with our yoga classes.',
                'price' => 25.00,
                'duration' => 60,
                'images' => [
                    'https://via.placeholder.com/300x200.png?text=Yoga+1',
                    'https://via.placeholder.com/300x200.png?text=Yoga+2',
                ],
            ],
            [
                'name' => 'Cooking Workshop',
                'description' => 'Learn to cook delicious meals with our experts.',
                'price' => 50.00,
                'duration' => 120,
                'images' => [
                    'https://via.placeholder.com/300x200.png?text=Cooking+1',
                ],
            ],
            [
                'name' => 'Art Therapy',
                'description' => 'Express yourself through creative art.',
                'price' => 30.00,
                'duration' => 90,
                'images' => [
                    'https://via.placeholder.com/300x200.png?text=Art+1',
                    'https://via.placeholder.com/300x200.png?text=Art+2',
                    'https://via.placeholder.com/300x200.png?text=Art+3',
                ],
            ],
        ];

        foreach ($categories as $category) {
            $categoryId = DB::table('categories')->insertGetId([
                'name' => $category['name'],
                'description' => $category['description'],
                'price' => $category['price'],
                'duration' => $category['duration'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($category['images'] as $imagePath) {
                DB::table('category_images')->insert([
                    'category_id' => $categoryId,
                    'image_path' => $imagePath,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
