<?php

namespace Config;

/**
 * Configuração central da aplicação: identidade, domínio e resolução de caminhos.
 *
 * Todos os links internos e assets devem passar por url()/asset()/absUrl()
 * (ver Core/helpers.php) em vez de caminhos fixos como "/cm1/...".
 */
class App
{
    public const BRAND_NAME = 'coninfoms';
    public const SITE_DOMAIN = 'coninfoms.com.br';
    public const SITE_URL = 'https://coninfoms.com.br';

    public const WHATSAPP_DEFAULT_NUMBER = '556799999999';
    public const WHATSAPP_DEFAULT_MESSAGE = 'Olá! Gostaria de mais informações sobre os serviços da coninfoms.';

    /**
     * Pasta (URL) onde o front controller (Public/index.php) está publicado.
     * Ex.: "/cm1/Public" (cPanel em subpasta) ou "" (document root = Public/).
     * Pode ser forçada com a variável de ambiente APP_PUBLIC_PATH.
     */
    public static function publicPath(): string
    {
        static $path = null;
        if ($path !== null) {
            return $path;
        }

        $forced = getenv('APP_PUBLIC_PATH');
        if ($forced !== false) {
            return $path = self::normalize($forced);
        }

        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        return $path = self::normalize(dirname($script));
    }

    /**
     * Raiz (URL) da aplicação, ou seja, o publicPath sem o sufixo "/Public".
     * Ex.: "/cm1" ou "".
     */
    public static function basePath(): string
    {
        static $path = null;
        if ($path !== null) {
            return $path;
        }

        $forced = getenv('APP_BASE_PATH');
        if ($forced !== false) {
            return $path = self::normalize($forced);
        }

        return $path = self::normalize(preg_replace('#/Public$#i', '', self::publicPath()));
    }

    /** Link interno da aplicação (ex.: url('materias') => "/cm1/materias"). */
    public static function url(string $path = ''): string
    {
        return self::basePath() . '/' . ltrim($path, '/');
    }

    /** Arquivo estático servido por Public/ (css, js, imagens, uploads). */
    public static function asset(string $path): string
    {
        return self::publicPath() . '/' . ltrim($path, '/');
    }

    /** URL absoluta no domínio oficial (canonical, compartilhamento, redes sociais). */
    public static function absUrl(string $path = ''): string
    {
        return self::SITE_URL . self::url($path);
    }

    /**
     * Converte a URI da requisição na rota interna, removendo a pasta base
     * onde o projeto está hospedado e o "index.php".
     */
    public static function routeFromRequestUri(string $requestUri): string
    {
        $uri = parse_url($requestUri, PHP_URL_PATH) ?: '/';

        $prefixes = array_unique([self::publicPath(), self::basePath()]);
        usort($prefixes, fn ($a, $b) => strlen($b) <=> strlen($a));

        foreach ($prefixes as $prefix) {
            if ($prefix !== '' && ($uri === $prefix || str_starts_with($uri, $prefix . '/'))) {
                $uri = substr($uri, strlen($prefix));
                break;
            }
        }

        $uri = preg_replace('#^/?index\.php#', '', $uri);

        return trim($uri, '/');
    }

    /** Normaliza um caminho para "/algo" (sem barra final) ou "" para a raiz. */
    private static function normalize(string $path): string
    {
        $path = trim(str_replace('\\', '/', $path), '/');
        $path = ($path === '' || $path === '.') ? '' : '/' . $path;
        return $path;
    }

    /**
     * Resolve links salvos no banco (ex.: "/cm1/pagina?slug=x") para a raiz correta
     * do servidor. Links externos, âncoras e vazios passam sem alteração.
     */
    public static function resolveLink(string $link): string
    {
        $link = trim($link);
        if ($link === '' || $link[0] === '#' || preg_match('#^([a-z][a-z0-9+.-]*:|//)#i', $link)) {
            return $link;
        }

        $path = ltrim($link, '/');
        if (str_starts_with($path, 'cm1/')) {
            $path = substr($path, 4);
        } elseif ($path === 'cm1') {
            $path = '';
        } elseif (self::basePath() !== '' && str_starts_with('/' . $path, self::basePath() . '/')) {
            return $link;
        }

        return self::url($path);
    }

    /** Corrige grafias da marca ("coinfoms", "CONINFOMS"...) para o nome oficial. */
    public static function fixBrand(string $text): string
    {
        return preg_replace('/\bco(?:n)?infoms\b/i', self::BRAND_NAME, $text) ?? $text;
    }

    /** Remove formulários HTML de conteúdo dinâmico (contato é apenas via WhatsApp). */
    public static function stripForms(string $html): string
    {
        return preg_replace('#<form\b[^>]*>.*?</form>#is', '', $html) ?? $html;
    }

    /** Monta o link wa.me a partir de número (qualquer formato) e mensagem. */
    public static function whatsappUrl(?string $number = null, ?string $message = null): string
    {
        $digits = preg_replace('/\D+/', '', $number ?? '');
        if ($digits === '') {
            $digits = self::WHATSAPP_DEFAULT_NUMBER;
        }

        $url = 'https://wa.me/' . $digits;
        $message = trim($message ?? '');
        if ($message !== '') {
            $url .= '?text=' . rawurlencode(self::fixBrand($message));
        }

        return $url;
    }
}
