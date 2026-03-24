<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advantages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('image')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->timestamps();
        });

        $now = now();

        DB::table('advantages')->insert([
            [
                'title' => 'Опытные инструкторы и большой автодром',
                'description' => 'Сертифицированные инструкторы с многолетним стажем обучают на собственном большом автодроме. Нет очередей, каждое занятие максимально результативно.',
                'image' => '/images/advantages/advantage-1-voditel.jpg',
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Современный автопарк',
                'description' => 'Обучение на автомобилях с механической и автоматической трансмиссией. Все машины регулярно проходят техобслуживание для вашей безопасности.',
                'image' => '/images/advantages/advantage-2-car.jpg',
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Гибкий график обучения',
                'description' => 'Практика проходит в светлое время суток с 8:00 до 17:00. Теоретические занятия проводятся вечером, чтобы обучение можно было совмещать с работой.',
                'image' => '/images/advantages/advantage-3-class.JPG',
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('advantages');
    }
};
