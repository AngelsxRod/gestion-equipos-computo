<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

final class Usuario
{
    public static function crear(string $nombreUsuario, string $password, string $rol): int
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare(
            'INSERT INTO usuarios (nombre_usuario, password_hash, rol)
             VALUES (:nombre_usuario, :password_hash, :rol)'
        );
        $stmt->execute([
            'nombre_usuario' => $nombreUsuario,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'rol' => $rol,
        ]);

        return (int) $pdo->lastInsertId();
    }
}
