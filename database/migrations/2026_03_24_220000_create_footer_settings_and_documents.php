<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('footer_settings', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->default('+7-961-526-23-59');
            $table->string('email')->default('avtoshkola-politekh@mail.ru');
            $table->text('address')->nullable();
            $table->text('logo_description')->nullable();
            $table->json('social_links')->nullable();
            $table->timestamps();
        });

        Schema::create('footer_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('footer_setting_id')->constrained('footer_settings')->cascadeOnDelete();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->string('title')->default('');
            $table->string('file_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique(['footer_setting_id', 'sort_order']);
        });

        $address = 'г. Краснодар, р-н Табачной фабрики, ул. Спортивная, д. 2, к. Л.';
        $logoDescription = 'Качественное обучение вождению для начинающих и профессионалов. Мы готовим водителей с гарантией успеха на дорогах.';

        $social = json_encode([
            ['code' => 'tg', 'url' => 'https://t.me/kubstu_official'],
            ['code' => 'vk', 'url' => 'https://vk.com/kubstu_official'],
        ], JSON_UNESCAPED_UNICODE);

        DB::table('footer_settings')->insert([
            'id' => 1,
            'phone' => '+7-961-526-23-59',
            'email' => 'avtoshkola-politekh@mail.ru',
            'address' => $address,
            'logo_description' => $logoDescription,
            'social_links' => $social,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        for ($i = 0; $i < 3; $i++) {
            DB::table('footer_documents')->insert([
                'footer_setting_id' => 1,
                'sort_order' => $i,
                'title' => $i === 0 ? 'Акт самообследования автошколы за 2024 год' : '',
                'file_path' => null,
                'original_filename' => null,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_documents');
        Schema::dropIfExists('footer_settings');
    }
};
