<?php

namespace Core;

use Config\App;

/**
 * Autenticação de sessão (único ponto de verdade).
 * Substitui checkAuth() duplicado em cada controller admin.
 */
class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] > 0;
    }

    public static function id(): ?int
    {
        return self::check() ? (int) $_SESSION['user_id'] : null;
    }

    public static function name(): string
    {
        return (string) ($_SESSION['user_name'] ?? '');
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = (string) ($user['name'] ?? '');
        $_SESSION['user_email'] = (string) ($user['email'] ?? '');
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: ' . App::url('login'));
            exit;
        }
    }
}
