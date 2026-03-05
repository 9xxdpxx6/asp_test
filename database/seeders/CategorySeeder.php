<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'А механика',
                'slug' => 'a-mehanika',
                'description' => '<p>Обучение на категорию A (механика). Практика на площадке и в городе, подготовка к экзамену ГИБДД.</p>',
                'price' => 40000,
                'icon' => 'fas fa-motorcycle',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/f/f7/Innocenti_Lambretta_125_D.jpg',
                'duration' => 60,
            ],
            [
                'name' => 'А+В механика (при одновременном обучении)',
                'slug' => 'a-plus-b-mehanika',
                'description' => '<p>Пакет A + B на механике. Стоимость: 30 000 + 70 000 ₽ при одновременном обучении.</p>',
                'price' => 100000,
                'icon' => 'fas fa-car-side',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/f/f5/5_Day_intensive_driving_course_cars_Cockfosters_01.jpg',
                'duration' => 90,
            ],
            [
                'name' => 'А+В автомат (при одновременном обучении)',
                'slug' => 'a-plus-b-avtomat',
                'description' => '<p>Пакет A + B на автомате. Стоимость: 30 000 + 70 000 ₽ при одновременном обучении.</p>',
                'price' => 100000,
                'icon' => 'fas fa-car',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/f/f8/5_Day_intensive_driving_course_cars_Cockfosters_02.jpg',
                'duration' => 90,
            ],
            [
                'name' => 'А индивидуальный курс (при наличии ранее выданного водительского удостоверения)',
                'slug' => 'a-individual',
                'description' => '<p>Индивидуальная программа по категории A для учеников с ранее выданным водительским удостоверением.</p>',
                'price' => 35000,
                'icon' => 'fas fa-user-graduate',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/e/e3/20160126_Sri_Lanka_3999_sRGB_%2825769639645%29.jpg',
                'duration' => 45,
            ],
            [
                'name' => 'В механика',
                'slug' => 'b-mehanika',
                'description' => '<p>Полный курс категории B на механике: теория, тренажер, площадка и город.</p>',
                'price' => 70000,
                'icon' => 'fas fa-car',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/7/73/Ford_Mustang_EcoBoost_RTR.jpg',
                'duration' => 75,
            ],
            [
                'name' => 'В автомат',
                'slug' => 'b-avtomat',
                'description' => '<p>Полный курс категории B на автоматической коробке передач.</p>',
                'price' => 70000,
                'icon' => 'fas fa-car',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/7/7b/Driving-school_sri-lanka.jpg',
                'duration' => 75,
            ],
            [
                'name' => 'В индивидуальный курс',
                'slug' => 'b-individual',
                'description' => '<p>Индивидуальный курс категории B с гибким графиком и персональным инструктором.</p>',
                'price' => 45000,
                'icon' => 'fas fa-user-clock',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/2/22/Driving_school_Hauer%2C_Baden.jpg',
                'duration' => 40,
            ],
        ];

        Category::query()
            ->whereNotIn('slug', array_column($categories, 'slug'))
            ->delete();

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
