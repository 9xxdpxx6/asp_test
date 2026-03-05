<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        $discounts = [
            [
                'name' => 'Скидка учащимся и студентам РФ',
                'slug' => 'skidka-uchashchimsya-i-studentam-rf',
                'percentage' => 10,
                'description' => 'Специальная скидка для учащихся и студентов при предъявлении подтверждающего документа.',
                'preview_path' => 'https://upload.wikimedia.org/wikipedia/commons/7/74/Bradford_School_of_Motoring_-_Lilycroft_Road_-_geograph.org.uk_-_2093045.jpg',
            ],
            [
                'name' => 'Скидка на медкомиссию',
                'slug' => 'skidka-na-medkomissiyu',
                'percentage' => 7,
                'description' => 'Партнерская скидка при прохождении водительской медицинской комиссии.',
                'preview_path' => 'https://upload.wikimedia.org/wikipedia/commons/4/40/Auto_%C3%A9cole_Pacifique_Cotonou_v%C3%A8doko.jpg',
            ],
            [
                'name' => 'Оплата материнским капиталом',
                'slug' => 'oplata-materinskim-kapitalom',
                'percentage' => 5,
                'description' => 'Возможность частичной оплаты обучения средствами материнского капитала по действующим правилам.',
                'preview_path' => 'https://upload.wikimedia.org/wikipedia/commons/3/3e/Fej%C3%A9r_Lip%C3%B3t_utca_63._A_Magyar_Aut%C3%B3klub_aut%C3%B3siskol%C3%A1ja_el%C5%91tti_parkol%C3%B3._Fortepan_87214.jpg',
            ],
        ];

        foreach ($discounts as &$discount) {
            $descriptionHtml = sprintf(
                '<p>%s</p><p>Скидка применяется при заключении договора и подтверждении основания. Детали условий уточняйте у администратора перед оплатой.</p><figure style="margin: 1.25rem 0;"><img src="%s" alt="%s" style="width:100%%;max-width:920px;height:auto;border-radius:12px;display:block;margin:0 auto;"><figcaption style="margin-top:.5rem;color:#6c757d;text-align:center;">Программа лояльности автошколы</figcaption></figure>',
                e($discount['description']),
                e($discount['preview_path']),
                e($discount['name'])
            );

            $discount['description'] = $descriptionHtml;
        }
        unset($discount);

        Discount::query()
            ->whereNotIn('slug', array_column($discounts, 'slug'))
            ->delete();

        foreach ($discounts as $discount) {
            Discount::updateOrCreate(
                ['slug' => $discount['slug']],
                $discount
            );
        }
    }
}
