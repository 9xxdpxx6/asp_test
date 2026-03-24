<?php

namespace App\Support;

final class FooterSocialCatalog
{
    /**
     * Порядок полей в админке (по одному URL на сеть).
     *
     * @return list<string>
     */
    public static function formRowOrder(): array
    {
        return ['vk', 'tg', 'max', 'ok', 'whatsapp', 'viber', 'dzen', 'mailru'];
    }

    /**
     * Файлы иконок в public/images/social/ (PNG/WebP/SVG — замените при необходимости).
     *
     * @return array<string, array{label: string, icon_file: string}>
     */
    public static function definitions(): array
    {
        return [
            'vk' => ['label' => 'ВКонтакте', 'icon_file' => 'vk.png'],
            'tg' => ['label' => 'Telegram', 'icon_file' => 'tg.png'],
            'max' => ['label' => 'MAX', 'icon_file' => 'max.png'],
            'ok' => ['label' => 'Одноклассники', 'icon_file' => 'ok.png'],
            'whatsapp' => ['label' => 'WhatsApp', 'icon_file' => 'whatsapp.png'],
            'viber' => ['label' => 'Viber', 'icon_file' => 'viber.png'],
            'dzen' => ['label' => 'Дзен', 'icon_file' => 'dzen.png'],
            'mailru' => ['label' => 'Mail.ru', 'icon_file' => 'mailru.png'],
        ];
    }

    public static function iconUrl(string $code): string
    {
        $defs = self::definitions();
        if (! isset($defs[$code])) {
            return asset('images/social/placeholder.png');
        }

        return asset('images/social/'.$defs[$code]['icon_file']);
    }

    public static function allowedCodes(): array
    {
        return array_keys(self::definitions());
    }

    /**
     * @param  array<int, array{code: string, url: string}>  $rows
     * @return array<int, array{code: string, url: string, label: string, icon_url: string}>
     */
    public static function normalizeSocialForPublic(array $rows): array
    {
        $defs = self::definitions();

        return collect($rows)
            ->filter(fn ($r) => is_array($r) && ! empty(trim((string) ($r['url'] ?? ''))))
            ->map(function (array $r) use ($defs): ?array {
                $code = (string) ($r['code'] ?? '');
                if (! isset($defs[$code])) {
                    return null;
                }
                $d = $defs[$code];

                return [
                    'code' => $code,
                    'url' => trim((string) ($r['url'])),
                    'label' => $d['label'],
                    'icon_url' => asset('images/social/'.$d['icon_file']),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
