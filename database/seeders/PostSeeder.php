<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Как выбрать программу обучения в автошколе',
                'slug' => 'kak-vybrat-programmu-obucheniya-v-avtoshkole',
                'preview_path' => 'https://upload.wikimedia.org/wikipedia/commons/f/f5/5_Day_intensive_driving_course_cars_Cockfosters_01.jpg',
                'content' => '<p>Перед записью сравните формат занятий, график практики и состав автопарка. Важно, чтобы программа совпадала с вашей целью и свободным временем.</p>',
            ],
            [
                'title' => '5 ошибок новичков на первых занятиях по вождению',
                'slug' => '5-oshibok-novichkov-na-pervyh-zanyatiyah-po-vozhdeniyu',
                'preview_path' => 'https://upload.wikimedia.org/wikipedia/commons/7/7b/Driving-school_sri-lanka.jpg',
                'content' => '<p>Чаще всего сложности возникают из-за спешки, неверной посадки и отсутствия контроля зеркал. Разберем, как избежать типичных ошибок уже на первых уроках.</p>',
            ],
            [
                'title' => 'Пошаговая подготовка к экзамену ГИБДД',
                'slug' => 'poshagovaya-podgotovka-k-ekzamenu-gibdd',
                'preview_path' => 'https://upload.wikimedia.org/wikipedia/commons/d/dd/Driver_training_nilson_nelson.jpg',
                'content' => '<p>Подготовка к экзамену включает теорию, отработку площадки и практику в городе. Четкий план тренировок заметно повышает шанс сдачи с первого раза.</p>',
            ],
            [
                'title' => 'Механика или автомат: что выбрать начинающему',
                'slug' => 'mehanika-ili-avtomat-chto-vybrat-nachinayushchemu',
                'preview_path' => 'https://upload.wikimedia.org/wikipedia/commons/7/73/Ford_Mustang_EcoBoost_RTR.jpg',
                'content' => '<p>Выбор зависит от ваших задач: универсальность и контроль на механике или более мягкий старт на автомате. Рассмотрим плюсы каждого варианта.</p>',
            ],
            [
                'title' => 'Как эффективно учить теорию ПДД',
                'slug' => 'kak-effektivno-uchit-teoriyu-pdd',
                'preview_path' => 'https://upload.wikimedia.org/wikipedia/commons/4/46/Betschdorf_Driving_School.jpg',
                'content' => '<p>Лучше всего работает короткая ежедневная практика, а не редкие длинные сессии. Используйте тематические блоки и регулярные мини-тесты.</p>',
            ],
            [
                'title' => 'Что взять с собой на первое практическое занятие',
                'slug' => 'chto-vzyat-s-soboy-na-pervoe-prakticheskoe-zanyatie',
                'preview_path' => 'https://upload.wikimedia.org/wikipedia/commons/7/75/Driving_school.jpg',
                'content' => '<p>Документы, удобная обувь и спокойный настрой. Инструктор объяснит базовые правила, а ваша задача — сосредоточиться на последовательности действий.</p>',
            ],
            [
                'title' => 'Сколько нужно часов практики, чтобы уверенно ездить',
                'slug' => 'skolko-nuzhno-chasov-praktiki-chtoby-uverenno-ezdit',
                'preview_path' => 'https://upload.wikimedia.org/wikipedia/commons/2/22/Driving_school_Hauer%2C_Baden.jpg',
                'content' => '<p>У каждого темп разный, но устойчивый прогресс дает регулярность. Лучше 2-3 занятия в неделю, чем редкие и длинные перерывы между уроками.</p>',
            ],
            [
                'title' => 'Вождение в городе: как снизить стресс',
                'slug' => 'vozhdenie-v-gorode-kak-snizit-stress',
                'preview_path' => 'https://upload.wikimedia.org/wikipedia/commons/d/d1/Stau_in_Stuttgart_auf_der_B14_%281%29.jpg',
                'content' => '<p>Работайте по маршрутам от простого к сложному, заранее планируйте маневры и держите безопасную дистанцию. Это снижает нагрузку и добавляет уверенности.</p>',
            ],
            [
                'title' => 'Как выбрать инструктора и получить максимум от занятий',
                'slug' => 'kak-vybrat-instruktora-i-poluchit-maksimum-ot-zanyatiy',
                'preview_path' => 'https://upload.wikimedia.org/wikipedia/commons/5/52/Colourful_Shops_Places_In_Onehunga_IV.jpg',
                'content' => '<p>Хороший инструктор дает понятную обратную связь и наращивает сложность постепенно. Не стесняйтесь обсуждать цели и зоны, которые нужно подтянуть.</p>',
            ],
            [
                'title' => 'Полезные привычки безопасного водителя',
                'slug' => 'poleznye-privychki-bezopasnogo-voditelya',
                'preview_path' => 'https://upload.wikimedia.org/wikipedia/commons/e/e3/20160126_Sri_Lanka_3999_sRGB_%2825769639645%29.jpg',
                'content' => '<p>Проверка зеркал, плавные маневры и прогноз дорожной ситуации делают поездку спокойнее. Эти привычки формируются регулярной практикой.</p>',
            ],
        ];

        Post::query()
            ->whereNotIn('slug', array_column($posts, 'slug'))
            ->delete();

        foreach ($posts as $index => $post) {
            Post::updateOrCreate(
                ['slug' => $post['slug']],
                array_merge($post, [
                    'created_at' => now()->subDays(10 - $index),
                    'updated_at' => now()->subDays(10 - $index),
                ])
            );
        }
    }
}
