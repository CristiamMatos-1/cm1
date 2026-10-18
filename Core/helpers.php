<?php

use Config\App;
use Core\Csrf;

if (!function_exists('url')) {
    function url(string $path = '', array $query = []): string
    {
        return App::url($path, $query);
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return App::asset($path);
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . e(Csrf::token()) . '">';
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Csrf::token();
    }
}

if (!function_exists('format_bytes')) {
    function format_bytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        if ($bytes < 1048576) {
            return number_format($bytes / 1024, 1, ',', '.') . ' KB';
        }
        return number_format($bytes / 1048576, 1, ',', '.') . ' MB';
    }
}
