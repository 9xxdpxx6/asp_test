<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Видео у слайда теперь необязательно (doctrine/dbal в проекте нет, поэтому ->change() недоступен)
        DB::statement('ALTER TABLE hero_slides MODIFY video_path VARCHAR(255) NULL');

        Schema::table('hero_slides', function (Blueprint $table) {
            // Что показывать рядом с текстом: video | photo | none
            $table->string('media_type', 8)->default('video');
            $table->string('image_path')->nullable();
            $table->unsignedSmallInteger('image_width')->default(0);
            $table->unsignedSmallInteger('image_height')->default(0);
            // Своё фото на фон слайда (необязательно)
            $table->string('bg_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropColumn(['media_type', 'image_path', 'image_width', 'image_height', 'bg_path']);
        });

        DB::table('hero_slides')->whereNull('video_path')->update(['video_path' => '']);
        DB::statement('ALTER TABLE hero_slides MODIFY video_path VARCHAR(255) NOT NULL');
    }
};
