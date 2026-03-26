<?php

namespace App\Support;

/**
 * Единый список целей для кнопок Hero на главной (публичное SPA).
 */
final class HeroCtaLinkCatalog
{
    /**
     * @return array<string, string> path => label
     */
    public static function options(): array
    {
        return [
            '/prices' => 'Раздел «Цены»',
            '/discounts' => 'Раздел «Программы лояльности»',
            '/blog' => 'Раздел «Новости»',
            '/contacts' => 'Контакты',
            '/about' => 'Раздел «О нас»',
            '#pricing-preview' => 'Блок «Стоимость» на главной (прокрутка вниз)',
            '#callback' => 'Форма «Обратный звонок» на главной',
        ];
    }

    /**
     * @return list<string>
     */
    public static function allowedPaths(): array
    {
        return array_keys(self::options());
    }

    public static function normalize(string $value, string $fallback): string
    {
        return in_array($value, self::allowedPaths(), true) ? $value : $fallback;
    }
}
