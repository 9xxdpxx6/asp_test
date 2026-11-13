<?php

namespace App\Support;

/**
 * Допустимые значения настроек слайда с видео и расчёт итоговых пропорций рамки.
 */
final class HeroSliderOptions
{
    /** Ниже этого соотношения сторон (ширина / высота) видео нельзя ставить фоном на всю шапку. */
    public const BACKGROUND_MIN_RATIO = 1.3;

    /**
     * @return array<string, string> значение => подпись
     */
    public static function ratios(): array
    {
        return [
            'auto' => 'Как в самом видео',
            '9:16' => '9:16 вертикальное (как сторис)',
            '4:5' => '4:5 почти квадрат',
            '1:1' => '1:1 квадрат',
            '4:3' => '4:3',
            '16:9' => '16:9 горизонтальное',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function fits(): array
    {
        return [
            'cover' => 'Обрезать по рамке (без полей)',
            'contain' => 'Показать целиком (с размытыми полями)',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function sides(): array
    {
        return [
            'right' => 'Справа от текста',
            'left' => 'Слева от текста',
            'background' => 'Фоном на всю шапку',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function sizes(): array
    {
        return [
            's' => 'Маленькое',
            'm' => 'Среднее',
            'l' => 'Большое',
        ];
    }

    /**
     * Что показывать рядом с текстом слайда.
     *
     * @return array<string, string>
     */
    public static function mediaTypes(): array
    {
        return [
            'video' => 'Видео',
            'photo' => 'Фото',
            'none' => 'Ничего (только текст)',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function textAligns(): array
    {
        return [
            'left' => 'Слева',
            'center' => 'По центру',
            'right' => 'Справа',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function textValigns(): array
    {
        return [
            'top' => 'Сверху',
            'center' => 'По центру',
            'bottom' => 'Снизу',
        ];
    }

    /**
     * @return list<int>
     */
    public static function autoplayChoices(): array
    {
        return [0, 6, 10, 15];
    }

    /**
     * Итоговое соотношение сторон рамки (ширина / высота).
     */
    public static function aspect(string $frameRatio, int $videoWidth, int $videoHeight): float
    {
        if ($frameRatio !== 'auto' && preg_match('/^(\d+):(\d+)$/', $frameRatio, $m) && (int) $m[2] > 0) {
            return (int) $m[1] / (int) $m[2];
        }

        if ($videoWidth > 0 && $videoHeight > 0) {
            return $videoWidth / $videoHeight;
        }

        return 9 / 16;
    }

    /**
     * Пропорции самого видео (а не выбранной рамки): по ним решается, можно ли ставить видео фоном.
     */
    public static function naturalAspect(int $videoWidth, int $videoHeight, string $frameRatio): float
    {
        if ($videoWidth > 0 && $videoHeight > 0) {
            return $videoWidth / $videoHeight;
        }

        return self::aspect($frameRatio, $videoWidth, $videoHeight);
    }

    /**
     * «Фоном» разрешено только для горизонтального видео: вертикальное при растягивании на всю
     * ширину шапки обрезает лица. В остальных случаях вместо фона ставится «справа».
     * Принимает пропорции самого видео (см. naturalAspect).
     */
    public static function normalizeSide(string $side, float $aspect): string
    {
        if (! array_key_exists($side, self::sides())) {
            return 'right';
        }

        if ($side === 'background' && $aspect < self::BACKGROUND_MIN_RATIO) {
            return 'right';
        }

        return $side;
    }
}
