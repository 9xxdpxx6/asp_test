<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('page_title');
            $table->string('page_subtitle')->nullable();
            $table->string('contacts_heading')->default('Свяжитесь с нами');
            $table->text('contacts_intro')->nullable();
            $table->json('phones')->nullable();
            $table->json('emails')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->string('title');
            $table->longText('map_embed_html')->nullable();
            $table->text('details_text')->nullable();
            $table->json('photos')->nullable();
            $table->timestamps();
        });

        $now = now();

        DB::table('contact_page_settings')->insert([
            'id' => 1,
            'page_title' => 'Наши адреса',
            'page_subtitle' => 'ул. Спортивная, 2кЛ | ул. Старокубанская, 88/5',
            'contacts_heading' => 'Свяжитесь с нами',
            'contacts_intro' => 'Мы всегда готовы ответить на ваши вопросы и помочь вам!',
            'phones' => json_encode(['+7-961-526-23-59']),
            'emails' => json_encode(['avtoshkola-politekh@mail.ru']),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $map1 = <<<'HTML'
<div style="position:relative;overflow:hidden;"><iframe src="https://yandex.ru/map-widget/v1/?ll=39.003489%2C45.043432&mode=search&sctx=ZAAAAAgBEAAaKAoSCYwubw7XfENAEZv%2B7EeKhEZAEhIJfEYiNIKNzz8RtJQsJ6H0uT8iBgABAgMEBSgKOABAI0gBagJydZ0BzczMPaABAKgBAL0BV1ve18IBBtGC5tGcBYICIdCw0LLRgtC%2B0YjQutC%2B0LvQsCDQv9C%2B0LvQuNGC0LXRhYoCAJICAJoCDGRlc2t0b3AtbWFwcw%3D%3D&sll=39.003489%2C45.043432&sspn=0.015407%2C0.006336&text=%D0%B0%D0%B2%D1%82%D0%BE%D1%88%D0%BA%D0%BE%D0%BB%D0%B0%20%D0%BF%D0%BE%D0%BB%D0%B8%D1%82%D0%B5%D1%85&whatshere%5Bpoint%5D=39.003489%2C45.043432&whatshere%5Bzoom%5D=16&z=17" width="100%" height="400" style="position:relative;border:0;" allowfullscreen="true"></iframe></div>
HTML;

        DB::table('contact_branches')->insert([
            [
                'sort_order' => 1,
                'title' => 'ул. Спортивная, 2кЛ',
                'map_embed_html' => $map1,
                'details_text' => "<strong>Адрес:</strong> г. Краснодар, р-н Табачной фабрики, ул. Спортивная, д. 2, к. Л.\n<strong>Режим работы:</strong> Пн–Пт 09:00–17:00, Сб–Вс — выходной",
                'photos' => json_encode(['/images/contacts/enter.JPG']),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'sort_order' => 2,
                'title' => 'ул. Старокубанская, 88/5',
                'map_embed_html' => null,
                'details_text' => "<strong>Адрес:</strong> г. Краснодар, ул. Старокубанская, 88/5\n<strong>Режим работы:</strong> Вт, Чт 09:00–12:00",
                'photos' => json_encode([
                    '/images/contacts/starokub-enter.jpg',
                    '/images/contacts/starokub-banner.jpg',
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_branches');
        Schema::dropIfExists('contact_page_settings');
    }
};
