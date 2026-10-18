<?php

namespace Core;

/**
 * Textos públicos da página de downloads e do link de menu "Suporte".
 * Valores vazios de título e do link voltam ao padrão; o texto da página pode ficar vazio.
 */
class SupportPageCopy
{
    public const KEY_LINK = 'support_link_label';
    public const KEY_TITLE = 'support_page_title';
    public const KEY_TEXT = 'support_page_text';

    public const DEFAULT_LINK = 'Suporte';
    public const DEFAULT_TITLE = 'Suporte Remoto';
    public const DEFAULT_TEXT = 'Baixe as ferramentas de acesso remoto e os arquivos de suporte disponibilizados pela CONINFOMS.';

    /**
     * @param array<string, mixed> $settings
     * @return array{link: string, title: string, text: string}
     */
    public static function fromMap(array $settings): array
    {
        return [
            'link' => self::linkLabel((string) ($settings[self::KEY_LINK] ?? '')),
            'title' => self::title((string) ($settings[self::KEY_TITLE] ?? '')),
            'text' => array_key_exists(self::KEY_TEXT, $settings)
                ? self::text((string) $settings[self::KEY_TEXT])
                : self::DEFAULT_TEXT,
        ];
    }

    public static function linkLabel(string $value): string
    {
        $value = self::plain($value, 80);
        return $value !== '' ? $value : self::DEFAULT_LINK;
    }

    public static function title(string $value): string
    {
        $value = self::plain($value, 160);
        return $value !== '' ? $value : self::DEFAULT_TITLE;
    }

    public static function text(string $value): string
    {
        return self::plain($value, 2000);
    }

    private static function plain(string $value, int $max): string
    {
        $value = trim(strip_tags($value));
        if (function_exists('mb_substr')) {
            return mb_substr($value, 0, $max);
        }
        return substr($value, 0, $max);
    }
}
