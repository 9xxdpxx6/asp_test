<?php

namespace Database\Seeders;

use App\Models\CallbackRequest;
use App\Models\Status;
use Illuminate\Database\Seeder;

class CallbackRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем все статусы из базы
        $statuses = Status::pluck('id')->toArray();

        if (empty($statuses)) {
            $this->command->error('No statuses found in the "statuses" table. Please seed the statuses first.');
            return;
        }

        foreach (range(1, 500) as $index) { // Генерируем 50 записей
            CallbackRequest::create([
                'full_name' => fake()->name,
                'phone' => fake()->phoneNumber,
                'email' => fake()->optional()->email, // Иногда email будет null
                'comment' => fake()->optional()->sentence, // Иногда комментарий будет null
                'note' => fake()->optional()->sentence, // Иногда примечание будет null
                'status_id' => $statuses[array_rand($statuses)], // Рандомный статус из базы
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
