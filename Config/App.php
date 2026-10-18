<?php

namespace Config;

/**
 * Configuração de aplicação (Single Responsibility).
 * Centraliza ambiente, caminho-base (/cm1) e URLs — evita hardcode espalhado.
 */
class App
{
    private static bool $booted = false;
    private static array $env = [];

    public static function boot(string $rootPath): void
    {
        if (self::$booted) {
            return;
        }

        self::$env['ROOT_PATH'] = rtrim($rootPath, '/');
        self::loadDotEnv(self::$env['ROOT_PATH'] . '/.env');
        self::$booted = true;
    }

    public static function rootPath(): string
    {
        return self::$env['ROOT_PATH'] ?? dirname(__DIR__);
    }

    public static function env(string $key, ?string $default = null): ?string
    {
        if (array_key_exists($key, $_ENV) && $_ENV[$key] !== '') {
            return (string) $_ENV[$key];
        }
        $fromServer = getenv($key);
        if ($fromServer !== false && $fromServer !== '') {
            return (string) $fromServer;
        }
        if (array_key_exists($key, self::$env) && self::$env[$key] !== '') {
            return (string) self::$env[$key];
        }
        return $default;
    }

    public static function isDebug(): bool
    {
        $value = strtolower((string) self::env('APP_DEBUG', '0'));
        return in_array($value, ['1', 'true', 'yes'], true);
    }

    /**
     * Prefixo público da aplicação (ex.: /cm1). Vazio se estiver na raiz do domínio.
     * Detecta automaticamente a partir de SCRIPT_NAME para funcionar no cPanel e no PHP built-in.
     */
    public static function basePath(): string
    {
        $configured = self::env('APP_BASE_PATH');
        if ($configured !== null && $configured !== '') {
            if ($configured === '/') {
                return '';
            }
            return '/' . trim($configured, '/');
        }

        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $dir = rtrim(dirname($script), '/');
        $dir = (string) preg_replace('#/Public$#i', '', $dir);

        if ($dir === '' || $dir === '/' || $dir === '.') {
            return '';
        }

        return $dir;
    }

    public static function url(string $path = '', array $query = []): string
    {
        $base = self::basePath();
        $path = trim($path, '/');

        if ($path === '') {
            $url = $base === '' ? '/' : $base . '/';
        } else {
            $url = ($base === '' ? '' : $base) . '/' . $path;
        }

        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    public static function asset(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        $base = self::basePath();
        return ($base === '' ? '' : $base) . '/Public' . $path;
    }

    public static function absoluteUrl(string $path = '', array $query = []): string
    {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? null) == 443)
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        $scheme = $https ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return $scheme . '://' . $host . self::url($path, $query);
    }

    private static function loadDotEnv(string $file): void
    {
        if (!is_readable($file)) {
            return;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (!str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $value = trim($value, "\"'");
            self::$env[$key] = $value;
            if (getenv($key) === false) {
                putenv($key . '=' . $value);
                $_ENV[$key] = $value;
            }
        }
    }
}
