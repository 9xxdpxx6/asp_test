<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('discounts')->insert([
            [
                'name' => 'Скидка на первые уроки',
                'percentage' => 20.00,
                'description' => 'Получите 20% скидку на первые 5 уроков в нашей автошколе.'
            ],
            [
                'name' => 'Семейные скидки',
                'percentage' => 15.00,
                'description' => 'Семейная скидка на обучение для двух и более членов семьи.'
            ],
            [
                'name' => 'Скидка для студентов',
                'percentage' => 10.00,
                'description' => 'Специальная скидка 10% для студентов на все курсы.'
            ],
            [
                'name' => 'Льгота для ветеранов',
                'percentage' => 30.00,
                'description' => 'Льгота 30% для ветеранов и их семей на все курсы.'
            ],
        ]);
    }
}
