<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_promos', function (Blueprint $table) {
            $table->id();
            // Заголовок необязателен — может быть только фото.
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            // Размер фото относительно блока: 3/4, 2/3, 1/2, 1/3, 1/4
            $table->string('image_size', 10)->default('1/2');
            // Расположение картинки внутри полосы (для top/bottom): true — слева, false — справа
            $table->boolean('image_on_left')->default(true);
            // Позиция относительно сетки цен: top | bottom | left | right
            $table->string('placement', 10)->default('top');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->timestamps();
        });

        // Демо-акция «Дорогу молодым» (фото загружается через админку).
        DB::table('price_promos')->insert([
            'title' => 'Дорогу молодым',
            'description' => "Абитуриент, успей забрать 10 000 рублей на обучение в Автошколе-Политех.\n\nАкция действует при условии заключения договора и оплаты обучения до 31.08.2026 г. (включительно). Срок акции может быть изменён.",
            'image' => null,
            'image_size' => '1/2',
            'image_on_left' => true,
            'placement' => 'top',
            'is_active' => true,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('price_promos');
    }
};
