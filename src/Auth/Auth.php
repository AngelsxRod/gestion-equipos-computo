<?php

declare(strict_types=1);

namespace App\Auth;

use App\Config\Database;
use PDO;

final class Auth
{
    public static function intentarLogin(string $nombreUsuario, string $password): bool
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare(
            'SELECT id, nombre_usuario, password_hash, rol
             FROM usuarios
             WHERE nombre_usuario = :nombre_usuario AND activo = 1'
        );
        $stmt->execute(['nombre_usuario' => $nombreUsuario]);
        $usuario = $stmt->fetch();

        if ($usuario === false || !password_verify($password, $usuario['password_hash'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['usuario_id'] = (int) $usuario['id'];
        $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
        $_SESSION['rol'] = $usuario['rol'];

        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }
}
