<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_section_settings', function (Blueprint $table) {
            $table->id();
            $table->string('section', 40)->unique();
            $table->string('heading');
            $table->string('subheading')->nullable();
            $table->timestamps();
        });

        Schema::create('home_feature_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('section', 40);
            $table->string('title');
            $table->text('description');
            $table->string('icon', 128);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('section');
        });

        $now = now();

        DB::table('home_section_settings')->insert([
            [
                'section' => 'why_choose_us',
                'heading' => 'Почему стоит выбрать именно нас',
                'subheading' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'section' => 'learning_process',
                'heading' => 'Как проходит обучение',
                'subheading' => 'Четыре простых шага от записи до получения прав',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $why = [
            ['title' => 'Опытные инструкторы', 'description' => 'Сертифицированные специалисты с многолетним стажем. Индивидуальный подход к каждому ученику и психологическая поддержка на всех этапах обучения.', 'icon' => 'fas fa-user-graduate'],
            ['title' => 'Удобный график', 'description' => 'Гибкое расписание практических занятий. Теория проходит в вечернее время — удобно совмещать с работой или учёбой.', 'icon' => 'fas fa-clock'],
            ['title' => 'Очная форма обучения', 'description' => 'Все занятия проходят очно с преподавателем. Вы можете задать вопросы и разобрать сложные ситуации в реальном времени.', 'icon' => 'fas fa-chalkboard-teacher'],
        ];
        foreach ($why as $i => $row) {
            DB::table('home_feature_blocks')->insert([
                'section' => 'why_choose_us',
                'title' => $row['title'],
                'description' => $row['description'],
                'icon' => $row['icon'],
                'sort_order' => $i + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $steps = [
            ['title' => 'Запись', 'description' => 'Оставьте заявку на сайте или позвоните нам. Мы подберём удобное расписание.', 'icon' => 'fas fa-file-signature'],
            ['title' => 'Теория', 'description' => 'Изучайте ПДД с опытными преподавателями в удобном формате.', 'icon' => 'fas fa-book-open'],
            ['title' => 'Практика', 'description' => 'Уроки вождения с персональным инструктором на современных автомобилях.', 'icon' => 'fas fa-car'],
            ['title' => 'Экзамен', 'description' => 'Подготовка к экзамену в ГИБДД и сопровождение до получения прав.', 'icon' => 'fas fa-trophy'],
        ];
        foreach ($steps as $i => $row) {
            DB::table('home_feature_blocks')->insert([
                'section' => 'learning_process',
                'title' => $row['title'],
                'description' => $row['description'],
                'icon' => $row['icon'],
                'sort_order' => $i + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('home_feature_blocks');
        Schema::dropIfExists('home_section_settings');
    }
};
