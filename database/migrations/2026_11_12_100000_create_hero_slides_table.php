<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->string('tab_label', 60);
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->string('button_label', 120)->nullable();
            $table->string('button_href', 500)->nullable();

            $table->string('video_path');
            $table->string('poster_path')->nullable();
            $table->string('preview_path')->nullable();
            $table->unsignedSmallInteger('video_width')->default(0);
            $table->unsignedSmallInteger('video_height')->default(0);
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->unsignedBigInteger('video_bytes')->default(0);

            // Как показывать: пропорции рамки, подгонка, сторона, размер
            $table->string('frame_ratio', 8)->default('auto');
            $table->string('fit_mode', 8)->default('cover');
            $table->string('media_side', 12)->default('right');
            $table->string('media_size', 2)->default('m');

            $table->timestamps();
        });

        Schema::table('hero_settings', function (Blueprint $table) {
            $table->string('main_slide_label', 60)->default('Об автошколе');
            $table->string('main_slide_position', 8)->default('last');
            $table->unsignedSmallInteger('slider_autoplay_seconds')->default(0);

            // Какие варианты показывать: слайдер в шапке и/или карточки категорий под шапкой
            $table->boolean('slider_in_hero')->default(true);
            $table->boolean('slider_show_cards')->default(false);
            $table->string('cards_title', 120)->default('Обучение по категориям');
            $table->string('cards_subtitle')->nullable()->default('Выберите свою категорию и посмотрите ролик');
        });
    }

    public function down(): void
    {
        Schema::table('hero_settings', function (Blueprint $table) {
            $table->dropColumn([
                'main_slide_label', 'main_slide_position', 'slider_autoplay_seconds',
                'slider_in_hero', 'slider_show_cards', 'cards_title', 'cards_subtitle',
            ]);
        });

        Schema::dropIfExists('hero_slides');
    }
};
