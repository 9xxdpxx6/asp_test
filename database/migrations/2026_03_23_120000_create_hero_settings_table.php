<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_settings', function (Blueprint $table) {
            $table->id();
            $table->string('background_image_path')->nullable();
            $table->string('title')->default('Автошкола Политех');
            $table->text('subtitle')->nullable();
            $table->string('buttons_align')->default('center');
            $table->string('buttons_direction')->default('row');
            $table->string('primary_button_label')->default('Узнать стоимость');
            $table->string('primary_button_icon')->default('fas fa-graduation-cap');
            $table->string('primary_button_href')->default('/prices');
            $table->string('secondary_button_label')->default('Записаться');
            $table->string('secondary_button_icon')->default('fas fa-phone');
            $table->string('secondary_button_href')->default('#pricing-preview');
            $table->json('trust_items');
            $table->timestamps();
        });

        $trustItems = [
            ['icon' => 'fas fa-map-marker-alt', 'value' => '2', 'label' => 'филиала в Краснодаре'],
            ['icon' => 'fas fa-shield-alt', 'value' => '', 'label' => 'Гос. лицензия'],
            ['icon' => 'fas fa-user-tie', 'value' => '', 'label' => 'Опытные инструкторы'],
            ['icon' => 'fas fa-car', 'value' => '', 'label' => 'Современный автопарк'],
        ];

        DB::table('hero_settings')->insert([
            'id' => 1,
            'background_image_path' => null,
            'title' => 'Автошкола Политех',
            'subtitle' => 'Обучение категорий A и B в Краснодаре. Опытные инструкторы, современные автомобили, удобное расписание.',
            'buttons_align' => 'center',
            'buttons_direction' => 'row',
            'primary_button_label' => 'Узнать стоимость',
            'primary_button_icon' => 'fas fa-graduation-cap',
            'primary_button_href' => '/prices',
            'secondary_button_label' => 'Записаться',
            'secondary_button_icon' => 'fas fa-phone',
            'secondary_button_href' => '#pricing-preview',
            'trust_items' => json_encode($trustItems, JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_settings');
    }
};
