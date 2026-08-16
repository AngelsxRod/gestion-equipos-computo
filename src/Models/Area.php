<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

final class Area
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function listarActivas(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT id, nombre FROM areas WHERE activo = 1 ORDER BY nombre');

        return $stmt->fetchAll();
    }

    public static function buscarPorId(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT id, nombre, activo FROM areas WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $area = $stmt->fetch();

        return $area === false ? null : $area;
    }

    public static function crear(string $nombre): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO areas (nombre) VALUES (:nombre)');
        $stmt->execute(['nombre' => $nombre]);

        return (int) $pdo->lastInsertId();
    }

    public static function actualizar(int $id, string $nombre): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE areas SET nombre = :nombre WHERE id = :id');
        $stmt->execute(['nombre' => $nombre, 'id' => $id]);
    }

    /**
     * Soft delete. Devuelve false si el área tiene empleados activos asociados.
     */
    public static function eliminar(int $id): bool
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM empleados WHERE area_id = :id AND activo = 1'
        );
        $stmt->execute(['id' => $id]);
        if ((int) $stmt->fetchColumn() > 0) {
            return false;
        }

        $stmt = $pdo->prepare('UPDATE areas SET activo = 0 WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return true;
    }
}
