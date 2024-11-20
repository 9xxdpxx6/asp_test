<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Новая', 'color' => '#007bff'],      // Синий
            ['name' => 'В процессе', 'color' => '#ffc107'], // Желтый
            ['name' => 'Завершена', 'color' => '#28a745'],  // Зеленый
            ['name' => 'Отменена', 'color' => '#dc3545'],   // Красный
        ];

        DB::table('statuses')->insert($statuses);
    }
}
