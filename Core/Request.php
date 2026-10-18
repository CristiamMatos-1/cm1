<?php

namespace Core;

use Config\App;

/**
 * Normaliza a URI da requisição.
 *
 * Raiz do bug /cm1: o front controller antigo só fazia str_replace('/cm1/', '/'),
 * que NÃO casa com "/cm1" (sem barra final). A URI virava "cm1" e o router
 * respondia 404. Aqui o prefixo é removido com e sem barra, e o caminho
 * residual "cm1"/"public" é tratado como home.
 */
class Request
{
    public static function method(?array $server = null): string
    {
        $server = $server ?? $_SERVER;
        return strtoupper($server['REQUEST_METHOD'] ?? 'GET');
    }

    public static function path(?array $server = null): string
    {
        $server = $server ?? $_SERVER;

        $uri = $server['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        if (!is_string($path) || $path === '') {
            $path = '/';
        }

        foreach (self::baseCandidates($server) as $base) {
            if (self::startsWithPath($path, $base)) {
                $path = substr($path, strlen($base)) ?: '/';
                break;
            }
        }

        $path = (string) preg_replace('#/index\.php$#i', '', $path);
        $path = trim($path, '/');

        // Defesa extra: se ainda restou o nome da pasta do projeto, é a home.
        if (in_array(strtolower($path), ['cm1', 'public', 'cm1/public'], true)) {
            return '';
        }

        return $path;
    }

    /**
     * @return string[] prefixos mais longos primeiro
     */
    private static function baseCandidates(array $server): array
    {
        $candidates = [];

        $script = str_replace('\\', '/', $server['SCRIPT_NAME'] ?? '');
        $scriptDir = rtrim(dirname($script), '/');
        if ($scriptDir !== '' && $scriptDir !== '/' && $scriptDir !== '.') {
            $candidates[] = $scriptDir;
            $withoutPublic = (string) preg_replace('#/Public$#i', '', $scriptDir);
            if ($withoutPublic !== $scriptDir && $withoutPublic !== '') {
                $candidates[] = $withoutPublic;
            }
        }

        $configured = App::basePath();
        if ($configured !== '') {
            $candidates[] = $configured . '/Public';
            $candidates[] = $configured;
        }

        $candidates[] = '/cm1/Public';
        $candidates[] = '/cm1';

        $candidates = array_values(array_unique(array_filter($candidates)));
        usort($candidates, static fn(string $a, string $b): int => strlen($b) <=> strlen($a));

        return $candidates;
    }

    private static function startsWithPath(string $path, string $base): bool
    {
        if ($base === '' || $base === '/') {
            return false;
        }

        if (strcasecmp($path, $base) === 0) {
            return true;
        }

        return strncasecmp($path, $base . '/', strlen($base) + 1) === 0;
    }
}
