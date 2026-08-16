<?php

declare(strict_types=1);

spl_autoload_register(function (string $clase): void {
    $prefijo = 'App\\';
    if (!str_starts_with($clase, $prefijo)) {
        return;
    }

    $rutaRelativa = substr($clase, strlen($prefijo));
    $rutaRelativa = str_replace('\\', '/', $rutaRelativa);

    // App\Config\Database vive en config/database.php (fuera de src/)
    if (str_starts_with($rutaRelativa, 'Config/')) {
        $nombreClase = substr($rutaRelativa, strlen('Config/'));
        $archivo = __DIR__ . '/config/' . strtolower($nombreClase) . '.php';
    } else {
        $archivo = __DIR__ . '/src/' . $rutaRelativa . '.php';
    }

    if (is_file($archivo)) {
        require_once $archivo;
    }
});

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

require_once __DIR__ . '/src/Helpers/helpers.php';
