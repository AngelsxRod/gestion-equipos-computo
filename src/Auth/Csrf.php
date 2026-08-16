<?php

declare(strict_types=1);

namespace App\Auth;

final class Csrf
{
    private const SESSION_KEY = 'csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public static function campoHtml(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . e(self::token()) . '">';
    }

    public static function validar(?string $tokenRecibido): bool
    {
        $tokenSesion = $_SESSION[self::SESSION_KEY] ?? '';

        $esValido = $tokenRecibido !== null
            && $tokenSesion !== ''
            && hash_equals($tokenSesion, $tokenRecibido);

        // Se regenera siempre tras validar, se haya usado o no, para evitar reintentos (replay).
        unset($_SESSION[self::SESSION_KEY]);

        return $esValido;
    }
}
