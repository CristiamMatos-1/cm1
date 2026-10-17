<?php

namespace Core;

/**
 * Sanitização de entrada. Substitui FILTER_SANITIZE_STRING (removido no PHP 8.1+).
 */
class Input
{
    public static function string(mixed $value): string
    {
        return trim(strip_tags((string) ($value ?? '')));
    }

    public static function email(mixed $value): string
    {
        $email = filter_var(trim((string) ($value ?? '')), FILTER_SANITIZE_EMAIL);
        return is_string($email) ? $email : '';
    }

    public static function int(mixed $value): ?int
    {
        $filtered = filter_var($value, FILTER_VALIDATE_INT);
        return $filtered === false ? null : $filtered;
    }
}
