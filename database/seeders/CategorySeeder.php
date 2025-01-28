<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Курс по вождению легкового автомобиля',
                'slug' => Str::slug('Курс по вождению легкового автомобиля'),
                'description' => '<p><strong>Что мы обучаем:</strong></p><p>Обучение управлению легковым автомобилем, включая основные принципы вождения, правила дорожного движения и навыки парковки.</p><img src="https://via.placeholder.com/600x300" alt="Driving Course Image" /><p><strong>Кому подходит:</strong> Для всех, кто хочет получить права категории B и научиться безопасно управлять автомобилем.</p>',
                'price' => 20000,
                'icon' => 'fas fa-car',
                'duration' => 60,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Курс по вождению мотоцикла',
                'slug' => Str::slug('Курс по вождению мотоцикла'),
                'description' => '<p><strong>Что мы обучаем:</strong></p><p>Обучение вождению мотоцикла, включая маневрирование на разных типах дорог и правильное использование мотоцикла в разных погодных условиях.</p><img src="https://via.placeholder.com/600x300" alt="Motorcycle Driving Course" /><p><strong>Кому подходит:</strong> Для тех, кто хочет получить права категории A и научиться безопасно управлять мотоциклом.</p>',
                'price' => 18000,
                'icon' => 'fas fa-motorcycle',
                'duration' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Курс по вождению грузового автомобиля',
                'slug' => Str::slug('Курс по вождению грузового автомобиля'),
                'description' => '<p><strong>Что мы обучаем:</strong></p><p>Обучение вождению грузовых автомобилей, включая особенности управления большими транспортными средствами, маневры на узких дорогах и безопасную эксплуатацию.</p><img src="https://via.placeholder.com/600x300" alt="Truck Driving Course" /><p><strong>Кому подходит:</strong> Для тех, кто хочет работать водителем грузового автомобиля и получить права категории C.</p>',
                'price' => 25000,
                'icon' => 'fas fa-truck',
                'duration' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Курс по вождению автобуса',
                'slug' => Str::slug('Курс по вождению автобуса'),
                'description' => '<p><strong>Что мы обучаем:</strong></p><p>Обучение вождению автобуса, включая правила безопасной перевозки пассажиров, маневрирование в городе и на трассах, а также управление автобусом в различных погодных условиях.</p><img src="https://via.placeholder.com/600x300" alt="Bus Driving Course" /><p><strong>Кому подходит:</strong> Для тех, кто хочет стать водителем автобуса и получить права категории D.</p>',
                'price' => 30000,
                'icon' => 'fas fa-bus',
                'duration' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Курс по вождению с прицепом',
                'slug' => Str::slug('Курс по вождению с прицепом'),
                'description' => '<p><strong>Что мы обучаем:</strong></p><p>Обучение вождению трактора, включая эксплуатацию сельскохозяйственной техники, особенности работы на фермерских участках и правила безопасной работы с трактором.</p><img src="https://via.placeholder.com/600x300" alt="Tractor Driving Course" /><p><strong>Кому подходит:</strong> Для тех, кто хочет работать с сельскохозяйственной техникой и получить права на трактор.</p>',
                'price' => 15000,
                'icon' => 'fas fa-tag',
                'duration' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        // Вставляем категории в таблицу
        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
