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
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'Категория A',
                'description' => 'Мотоциклы',
                'price' => 15000.00,
                'duration' => 1.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Категория B',
                'description' => 'Легковые автомобили',
                'price' => 25000.00,
                'duration' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Категория C',
                'description' => 'Грузовые автомобили',
                'price' => 30000.00,
                'duration' => 2.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Категория D',
                'description' => 'Автобусы',
                'price' => 40000.00,
                'duration' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Категория E',
                'description' => 'Автопоезда (с прицепом)',
                'price' => 45000.00,
                'duration' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
