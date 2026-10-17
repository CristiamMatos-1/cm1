<?php

namespace Core;

class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validate(?string $token): bool
    {
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        if (!is_string($sessionToken) || $sessionToken === '' || !is_string($token) || $token === '') {
            return false;
        }
        return hash_equals($sessionToken, $token);
    }

    public static function assertValid(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!self::validate(is_string($token) ? $token : null)) {
            http_response_code(419);
            die('Sessão expirada ou requisição inválida. Recarregue a página e tente novamente.');
        }
    }
}
