<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Убедитесь, что путь к вашей модели User правильный

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создание одного пользователя
        User::create([
            'name' => 'qwe', // Фиксированное имя
            'email' => 'qwe@qwe.qwe',
            'password' => Hash::make('qwe'),
        ]);
    }
}
