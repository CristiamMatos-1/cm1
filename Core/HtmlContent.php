<?php

namespace Core;

/**
 * Conteúdo rich-text (TinyMCE): UTF-8 nativo, sem named entities de acentos.
 */
class HtmlContent
{
    /**
     * Converte &ccedil; / &#231; etc. para caracteres UTF-8 e remove tags perigosas.
     */
    public static function normalize(string $html): string
    {
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $html = preg_replace('/\x00/', '', $html) ?? $html;

        $allowed = '<p><br><strong><b><em><i><u><s><ul><ol><li><a><h1><h2><h3><h4><h5><h6>'
            . '<blockquote><pre><code><img><figure><figcaption><span><div>'
            . '<table><thead><tbody><tfoot><tr><th><td><hr><sub><sup>';

        $clean = strip_tags($html, $allowed);
        $clean = preg_replace('/\s(on\w+|style)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean) ?? $clean;
        $clean = preg_replace('/javascript\s*:/i', '', $clean) ?? $clean;

        return trim($clean);
    }

    /**
     * Texto puro para listagens (acentos legíveis, sem &ccedil;).
     */
    public static function excerpt(string $html, int $max = 180): string
    {
        $html = preg_replace('/<\s*br\s*\/?\s*>/i', ' ', $html) ?? $html;
        $html = preg_replace('/<\/\s*(p|div|li|h[1-6]|tr)\s*>/i', ' ', $html) ?? $html;
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', trim($text)) ?? '';
        if (mb_strlen($text) <= $max) {
            return $text;
        }
        return rtrim(mb_substr($text, 0, $max), " \t.,;:") . '…';
    }

    /**
     * Capa da matéria ou logo do site.
     */
    public static function coverImage(?array $article, string $fallbackLogo = ''): string
    {
        $path = trim((string) ($article['featured_image'] ?? ''));
        if ($path !== '') {
            return $path;
        }
        return trim($fallbackLogo);
    }
}
