<?php

namespace App\Support;

class ReviewWidgetCatalog
{
    public const YANDEX_ORG_ID = '179486425425';
    public const TWOGIS_ORG_ID = '70000001046117787';
    public const TWOGIS_FIRM_ID = '70000001046117788';

    public static function defaults(): array
    {
        $yandexReviewsUrl = 'https://yandex.ru/maps/org/politekh/' . self::YANDEX_ORG_ID . '/reviews/';
        $yandexAddReviewUrl = $yandexReviewsUrl . '?add-review';
        $twogisReviewsUrl = 'https://2gis.ru/krasnodar/branches/' . self::TWOGIS_ORG_ID . '/firm/' . self::TWOGIS_FIRM_ID . '/39.003565%2C45.043388/tab/reviews?m=38.971526%2C45.024767%2F10.79';
        $twogisAddReviewUrl = $twogisReviewsUrl . '&addReview';

        return [
            [
                'slug' => 'twogis_constructor_big',
                'title' => '2ГИС: лента отзывов',
                'description' => 'Большой фирменный виджет 2ГИС с реальной лентой отзывов.',
                'provider' => '2gis',
                'render_type' => '2gis_constructor',
                'show_on_home' => true,
                'home_sort_order' => 1,
                'is_active' => true,
                'config' => [
                    'service_label' => '2ГИС',
                    'card_title' => 'Отзывы в 2ГИС',
                    'card_text' => 'Большой фирменный виджет с лентой отзывов и прямой кнопкой в карточку организации.',
                    'size' => 'big',
                    'theme' => 'light',
                    'org_id' => self::TWOGIS_ORG_ID,
                    'branch_id' => '',
                    'height' => 824,
                    'button_text' => 'Оставить отзыв',
                    'button_url' => $twogisAddReviewUrl,
                    'button_variant' => 'primary',
                ],
            ],
            [
                'slug' => 'yandex_comments',
                'title' => 'Яндекс: лента отзывов',
                'description' => 'Официальный iframe-виджет Яндекс Карт с комментариями.',
                'provider' => 'yandex',
                'render_type' => 'iframe_src',
                'show_on_home' => true,
                'home_sort_order' => 2,
                'is_active' => true,
                'config' => [
                    'service_label' => 'Яндекс Карты',
                    'card_title' => 'Отзывы на Яндексе',
                    'card_text' => 'Официальный виджет Яндекс Карт с отзывами и кнопкой перехода на форму добавления.',
                    'src' => 'https://yandex.ru/maps-reviews-widget/' . self::YANDEX_ORG_ID . '?comments',
                    'height' => 824,
                    'button_text' => 'Оставить отзыв',
                    'button_url' => $yandexAddReviewUrl,
                    'button_variant' => 'primary',
                ],
            ],
            [
                'slug' => 'twogis_badge_qr',
                'title' => '2ГИС: рейтинг + QR',
                'description' => 'Компактная карточка 2ГИС с рейтингом, звёздами, кнопкой и QR.',
                'provider' => '2gis',
                'render_type' => 'badge_qr',
                'show_on_home' => false,
                'home_sort_order' => null,
                'is_active' => true,
                'config' => [
                    'service_label' => '2ГИС',
                    'card_title' => 'Рейтинг + QR на отзыв',
                    'card_text' => 'Мини-формат: рейтинг, звёзды, переход в отзывы и QR-код для быстрого открытия.',
                    'badge_src' => 'https://widget.2gis.ru/api/widget?org_id=' . self::TWOGIS_ORG_ID . '&size=small&theme=light',
                    'badge_height' => 210,
                    'rating_value' => '5.0',
                    'review_count_text' => '32 отзыва',
                    'compact_link_text' => 'Посмотреть и оставить отзыв в 2ГИС',
                    'qr_url' => $twogisAddReviewUrl,
                    'button_text' => 'Оставить отзыв',
                    'button_url' => $twogisAddReviewUrl,
                    'button_variant' => 'primary',
                ],
            ],
            [
                'slug' => 'yandex_badge_qr',
                'title' => 'Яндекс: рейтинг + QR',
                'description' => 'Компактная карточка Яндекса с рейтингом, звёздами, кнопкой и QR.',
                'provider' => 'yandex',
                'render_type' => 'badge_qr',
                'show_on_home' => false,
                'home_sort_order' => null,
                'is_active' => true,
                'config' => [
                    'service_label' => 'Яндекс Карты',
                    'card_title' => 'Рейтинг + QR на отзыв',
                    'card_text' => 'Мини-формат: рейтинг, звёзды, переход в отзывы и QR-код для быстрого открытия.',
                    'badge_src' => 'https://yandex.ru/maps-reviews-widget/' . self::YANDEX_ORG_ID,
                    'badge_height' => 210,
                    'rating_value' => '5.0',
                    'review_count_text' => '55 отзывов',
                    'compact_link_text' => 'Посмотреть и оставить отзыв на Яндекс Картах',
                    'qr_url' => $yandexAddReviewUrl,
                    'button_text' => 'Оставить отзыв',
                    'button_url' => $yandexAddReviewUrl,
                    'button_variant' => 'primary',
                ],
            ],
        ];
    }

    public static function homeDefaults(): array
    {
        return array_values(array_filter(
            self::defaults(),
            static fn (array $item) => (bool) ($item['show_on_home'] ?? false)
        ));
    }
}
