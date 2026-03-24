<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_section_settings', function (Blueprint $table) {
            $table->json('payload')->nullable()->after('subheading');
        });

        $now = now();

        DB::table('home_section_settings')->updateOrInsert(
            ['section' => 'callback_form'],
            [
                'heading' => 'Готовы начать обучение?',
                'subheading' => 'Оставьте заявку, и мы свяжемся с вами, чтобы ответить на все вопросы и помочь с записью.',
                'payload' => json_encode([
                    'phone_label' => '+7 (961) 526-23-59',
                    'form_title' => 'Обратный звонок',
                    'name_placeholder' => 'Ваше имя *',
                    'phone_placeholder' => 'Телефон *',
                    'email_placeholder' => 'Электронная почта',
                    'comment_placeholder' => 'Комментарий',
                    'button_text' => 'Отправить заявку',
                ], JSON_UNESCAPED_UNICODE),
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }

    public function down(): void
    {
        DB::table('home_section_settings')
            ->where('section', 'callback_form')
            ->delete();

        Schema::table('home_section_settings', function (Blueprint $table) {
            $table->dropColumn('payload');
        });
    }
};
