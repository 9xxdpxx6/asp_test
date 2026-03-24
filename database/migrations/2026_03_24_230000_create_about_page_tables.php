<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hero_title');
            $table->string('hero_subtitle')->nullable();
            $table->string('cta_title');
            $table->text('cta_text');
            $table->string('cta_button_text');
            $table->string('cta_route_name')->default('prices');
            $table->timestamps();
        });

        Schema::create('about_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description');
            $table->string('image')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->boolean('image_on_left')->default(true);
            $table->timestamps();
        });

        $now = now();

        DB::table('about_settings')->insert([
            'id' => 1,
            'hero_title' => 'О нашей автошколе',
            'hero_subtitle' => 'Добро пожаловать в «Автошкола Политех»',
            'cta_title' => 'Готовим грамотных водителей',
            'cta_text' => 'В условиях сложной дорожной обстановки мы стремимся выпускать уверенных водителей, готовых к любым вызовам на дороге. Хотите стать таким водителем? Мы ждем вас!',
            'cta_button_text' => 'Записаться на обучение',
            'cta_route_name' => 'prices',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('about_blocks')->insert([
            [
                'title' => null,
                'description' => "Автошкола «Политех» открыла свои двери для всех желающих обучиться вождению в 2012 году.\nС тех пор мы успешно обучили тысячи учеников в 8 филиалах, удобно расположенных по всему Краснодару.\n\nНаши современные автодромы с макетом реальных дорог обеспечивают идеальные условия для практических занятий, где начинающие водители могут отточить свои навыки вождения под руководством опытных инструкторов.",
                'image' => '/images/about/drom.jpg',
                'sort_order' => 1,
                'image_on_left' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Обучение с комфортом',
                'description' => "Мы понимаем, что комфортное обучение — это залог успеха. Каждый ученик получает необходимые учебные материалы, выбирает инструктора и составляет индивидуальный график занятий.\n\nВы можете выбрать автомобиль из нашего автопарка — как отечественные модели, так и иномарки. Занятия проходят в небольших группах, что позволяет уделять внимание каждому ученику.",
                'image' => '/images/about/comfort.jpg',
                'sort_order' => 2,
                'image_on_left' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Очная форма обучения',
                'description' => "Все занятия проходят в очном формате с опытным преподавателем. Вы можете задать любые вопросы, разобрать сложные ситуации на дороге.\n\nМы предоставляем доступ к теоретическим материалам, а также возможность пройти пробный экзамен, аналогичный экзамену в ГИБДД.",
                'image' => '/images/about/online.jpg',
                'sort_order' => 3,
                'image_on_left' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Программы лояльности',
                'description' => 'Мы предлагаем различные программы лояльности, которые помогут вам сэкономить. Скидки студентам, оплата материнским капиталом и возможность рассрочки.',
                'image' => '/images/about/loyalty.jpg',
                'sort_order' => 4,
                'image_on_left' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('about_blocks');
        Schema::dropIfExists('about_settings');
    }
};
