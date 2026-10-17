<?php

use Config\App;

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        return App::url($path);
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return App::asset($path);
    }
}

if (!function_exists('abs_url')) {
    function abs_url(string $path = ''): string
    {
        return App::absUrl($path);
    }
}

if (!function_exists('resolve_link')) {
    function resolve_link(?string $link): string
    {
        return App::resolveLink((string) $link);
    }
}

if (!function_exists('e')) {
    /** Escapa para HTML aplicando a grafia oficial da marca. */
    function e(?string $text): string
    {
        return htmlspecialchars(App::fixBrand((string) $text), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('brand_html')) {
    /** Conteúdo HTML vindo do banco: corrige a marca e remove formulários. Não altera o banco. */
    function brand_html(?string $html): string
    {
        return App::stripForms(App::fixBrand((string) $html));
    }
}

if (!function_exists('whatsapp_link')) {
    /** Link wa.me com os dados configurados no painel (ou o padrão da marca). */
    function whatsapp_link(?string $message = null): string
    {
        static $number = null, $defaultMessage = null;

        if ($number === null) {
            try {
                $settings = new \Models\Setting();
                $number = $settings->get('whatsapp_number', App::WHATSAPP_DEFAULT_NUMBER);
                $defaultMessage = $settings->get('whatsapp_message', App::WHATSAPP_DEFAULT_MESSAGE);
            } catch (\Throwable $e) {
                $number = App::WHATSAPP_DEFAULT_NUMBER;
                $defaultMessage = App::WHATSAPP_DEFAULT_MESSAGE;
            }
        }

        return App::whatsappUrl($number, $message ?? $defaultMessage);
    }
}
