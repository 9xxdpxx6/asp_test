<?php

namespace App\Support;

class HtmlEntityDecoder
{
    public static function decodeString(?string $value): ?string
    {
        if (!is_string($value) || $value === '') {
            return $value;
        }

        if (!str_contains($value, '&')) {
            return $value;
        }

        return html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public static function decodeRecursive($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = self::decodeRecursive($item);
            }

            return $value;
        }

        if (is_string($value)) {
            return self::decodeString($value);
        }

        return $value;
    }
}
