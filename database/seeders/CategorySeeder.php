<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'A механика',
                'slug' => 'a-mehanika',
                'description' => 'Обучение на категорию A с упором на устойчивые навыки в реальном городском потоке.',
                'price' => 40000,
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/f/f7/Innocenti_Lambretta_125_D.jpg',
                'program' => 'A',
                'transmission' => 'Механика',
                'format' => 'group',
                'theory_hours' => 134,
                'practice_hours' => 18,
                'gallery' => [
                    'https://upload.wikimedia.org/wikipedia/commons/7/75/Driving_school.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/d/dd/Driver_training_nilson_nelson.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/f/f7/Innocenti_Lambretta_125_D.jpg',
                ],
            ],
            [
                'name' => 'A+B механика (при одновременном обучении)',
                'slug' => 'a-plus-b-mehanika',
                'description' => 'Комбинированная программа категорий A и B на механической коробке передач.',
                'price' => 100000,
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/f/f5/5_Day_intensive_driving_course_cars_Cockfosters_01.jpg',
                'program' => 'A+B',
                'transmission' => 'Механика',
                'format' => 'combo',
                'theory_hours' => 188,
                'practice_hours' => 56,
                'gallery' => [
                    'https://upload.wikimedia.org/wikipedia/commons/f/f5/5_Day_intensive_driving_course_cars_Cockfosters_01.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/7/75/Driving_school.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/d/dd/Driver_training_nilson_nelson.jpg',
                ],
            ],
            [
                'name' => 'A+B автомат (при одновременном обучении)',
                'slug' => 'a-plus-b-avtomat',
                'description' => 'Комбинированный курс A+B на АКПП с акцентом на уверенный старт и безопасный город.',
                'price' => 100000,
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/f/f8/5_Day_intensive_driving_course_cars_Cockfosters_02.jpg',
                'program' => 'A+B',
                'transmission' => 'Автомат',
                'format' => 'combo',
                'theory_hours' => 188,
                'practice_hours' => 56,
                'gallery' => [
                    'https://upload.wikimedia.org/wikipedia/commons/f/f8/5_Day_intensive_driving_course_cars_Cockfosters_02.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/7/7b/Driving-school_sri-lanka.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/d/dd/Driver_training_nilson_nelson.jpg',
                ],
            ],
            [
                'name' => 'A индивидуальный курс',
                'slug' => 'a-individual',
                'description' => 'Индивидуальный формат для категории A: гибкий график и персональная траектория подготовки.',
                'price' => 35000,
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/e/e3/20160126_Sri_Lanka_3999_sRGB_%2825769639645%29.jpg',
                'program' => 'A',
                'transmission' => 'Механика',
                'format' => 'individual',
                'theory_hours' => 134,
                'practice_hours' => 20,
                'gallery' => [
                    'https://upload.wikimedia.org/wikipedia/commons/e/e3/20160126_Sri_Lanka_3999_sRGB_%2825769639645%29.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/7/75/Driving_school.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/d/dd/Driver_training_nilson_nelson.jpg',
                ],
            ],
            [
                'name' => 'B механика',
                'slug' => 'b-mehanika',
                'description' => 'Полный курс категории B на МКПП: от теории до уверенного движения в городе.',
                'price' => 70000,
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/7/73/Ford_Mustang_EcoBoost_RTR.jpg',
                'program' => 'B',
                'transmission' => 'Механика',
                'format' => 'group',
                'theory_hours' => 134,
                'practice_hours' => 56,
                'gallery' => [
                    'https://upload.wikimedia.org/wikipedia/commons/7/73/Ford_Mustang_EcoBoost_RTR.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/2/22/Driving_school_Hauer%2C_Baden.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/7/75/Driving_school.jpg',
                ],
            ],
            [
                'name' => 'B автомат',
                'slug' => 'b-avtomat',
                'description' => 'Категория B на АКПП: комфортный вход в обучение и акцент на практику в трафике.',
                'price' => 70000,
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/7/7b/Driving-school_sri-lanka.jpg',
                'program' => 'B',
                'transmission' => 'Автомат',
                'format' => 'group',
                'theory_hours' => 134,
                'practice_hours' => 54,
                'gallery' => [
                    'https://upload.wikimedia.org/wikipedia/commons/7/7b/Driving-school_sri-lanka.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/f/f8/5_Day_intensive_driving_course_cars_Cockfosters_02.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/2/22/Driving_school_Hauer%2C_Baden.jpg',
                ],
            ],
            [
                'name' => 'B индивидуальный курс',
                'slug' => 'b-individual',
                'description' => 'Персональный курс категории B с гибким расписанием и адаптацией под ваш опыт.',
                'price' => 45000,
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/2/22/Driving_school_Hauer%2C_Baden.jpg',
                'program' => 'B',
                'transmission' => 'Автомат',
                'format' => 'individual',
                'theory_hours' => 134,
                'practice_hours' => 24,
                'gallery' => [
                    'https://upload.wikimedia.org/wikipedia/commons/2/22/Driving_school_Hauer%2C_Baden.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/d/dd/Driver_training_nilson_nelson.jpg',
                    'https://upload.wikimedia.org/wikipedia/commons/7/75/Driving_school.jpg',
                ],
            ],
        ];

        Category::query()
            ->whereNotIn('slug', array_column($categories, 'slug'))
            ->delete();

        foreach ($categories as $categoryData) {
            $category = Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'name' => $categoryData['name'],
                    'slug' => $categoryData['slug'],
                    'description' => '<p>' . $categoryData['description'] . '</p>',
                    'price' => $categoryData['price'],
                    'image' => $categoryData['image'],
                    // Убираем legacy-поля для карточек категории.
                    'icon' => null,
                    'duration' => null,
                ]
            );

            $category->blocks()->delete();

            foreach ($this->buildBlocks($categoryData) as $index => $block) {
                $category->blocks()->create([
                    'type' => $block['type'],
                    'content' => $block['content'],
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }

    private function buildBlocks(array $category): array
    {
        $price = number_format((int) $category['price'], 0, ',', ' ');
        $isIndividual = $category['format'] === 'individual';
        $isCombo = $category['format'] === 'combo';
        $program = $category['program'];
        $transmission = $category['transmission'];

        $intro = sprintf(
            '<h3>Как проходит обучение на %s</h3><p>Программа построена по модели современных автошкол: сначала базовая теория и разбор типовых дорожных сценариев, затем пошаговая практика с наращиванием сложности маршрутов.</p><p>Формат курса: <strong>%s</strong>. Транспорт: <strong>%s</strong>. Практика ведется по маршрутам, близким к экзаменационным, с отдельным блоком на маневры и безопасность.</p>',
            e($category['name']),
            $isIndividual ? 'индивидуальный' : 'групповой',
            e($transmission)
        );

        $imageText = sprintf(
            '<p>На этом этапе вы отрабатываете ключевые сценарии: старт и остановка, безопасные перестроения, повороты, парковка, движение в плотном потоке и разбор сложных перекрестков.</p><p>%s</p>',
            $isCombo
                ? 'В комбинированной программе A+B практические занятия чередуются по категориям, чтобы не терять темп и закреплять навыки сразу в двух типах ТС.'
                : 'Инструктор фиксирует прогресс по чек-листу навыков и дает конкретные рекомендации после каждого занятия.'
        );

        return [
            [
                'type' => 'text',
                'content' => [
                    'html' => $intro,
                ],
            ],
            [
                'type' => 'features',
                'content' => [
                    'title' => 'Что вы получаете на курсе',
                    'items' => [
                        [
                            'icon' => 'fas fa-chalkboard-teacher',
                            'title' => 'Теория без воды',
                            'text' => 'Структурированные занятия по ПДД, реальным дорожным кейсам и типовым ошибкам на экзамене.',
                        ],
                        [
                            'icon' => 'fas fa-route',
                            'title' => 'Практика по маршрутам',
                            'text' => 'Отработка экзаменационных элементов и городских сценариев с поэтапным ростом сложности.',
                        ],
                        [
                            'icon' => 'fas fa-user-check',
                            'title' => 'Подготовка к экзамену',
                            'text' => 'Финальные тренировки под формат ГИБДД и рекомендации по слабым зонам.',
                        ],
                    ],
                ],
            ],
            [
                'type' => 'image_text',
                'content' => [
                    'title' => 'Практический этап',
                    'layout' => 'left',
                    'image_size' => '1/2',
                    'image_url' => $category['image'],
                    'html' => $imageText,
                ],
            ],
            [
                'type' => 'pricing',
                'content' => [
                    'title' => 'Состав стоимости',
                    'items' => [
                        ['label' => 'Стоимость курса', 'value' => $price . ' руб.'],
                        ['label' => 'Теория', 'value' => $category['theory_hours'] . ' акад. часов'],
                        ['label' => 'Практика', 'value' => $category['practice_hours'] . ' часов вождения'],
                        ['label' => 'Тип программы', 'value' => $isIndividual ? 'Индивидуальная' : 'Стандартная'],
                        ['label' => 'Категория', 'value' => $program],
                    ],
                ],
            ],
            [
                'type' => 'faq',
                'content' => [
                    'title' => 'Частые вопросы',
                    'items' => [
                        [
                            'question' => 'Можно ли платить частями?',
                            'answer' => 'Да, доступна поэтапная оплата по графику. Сумма и этапы фиксируются в договоре.',
                        ],
                        [
                            'question' => 'Как формируется график вождения?',
                            'answer' => $isIndividual
                                ? 'График согласуется персонально под вашу занятость, включая вечерние окна и выходные.'
                                : 'График формируется заранее на неделю вперед с возможностью переноса по согласованию.',
                        ],
                        [
                            'question' => 'Что входит в подготовку к экзамену?',
                            'answer' => 'Контрольные занятия, разбор типичных ошибок и тренировка экзаменационного маршрута.',
                        ],
                    ],
                ],
            ],
            [
                'type' => 'gallery',
                'content' => [
                    'title' => 'Как проходят занятия',
                    'images' => array_map(static function (string $url): array {
                        return [
                            'url' => $url,
                            'alt' => 'Учебный процесс автошколы',
                        ];
                    }, $category['gallery']),
                ],
            ],
        ];
    }
}
