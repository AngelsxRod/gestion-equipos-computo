<?php

declare(strict_types=1);

// Script CLI para crear el usuario administrador inicial.
// Uso: php sql/seed.php

require_once __DIR__ . '/../bootstrap.php';

use App\Models\Usuario;

if (PHP_SAPI !== 'cli') {
    exit('Este script solo puede ejecutarse desde la línea de comandos.' . PHP_EOL);
}

fwrite(STDOUT, 'Nombre de usuario para el administrador: ');
$nombreUsuario = trim((string) fgets(STDIN));

fwrite(STDOUT, 'Contraseña (mínimo 8 caracteres): ');
$password = trim((string) fgets(STDIN));

if ($nombreUsuario === '' || strlen($password) < 8) {
    exit('Nombre de usuario vacío o contraseña demasiado corta.' . PHP_EOL);
}

Usuario::crear($nombreUsuario, $password, 'admin');

echo 'Usuario administrador "' . $nombreUsuario . '" creado correctamente.' . PHP_EOL;
