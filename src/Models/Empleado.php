<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

final class Empleado
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function listarActivos(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query(
            'SELECT empleados.id, empleados.nombre_completo, empleados.codigo_empleado,
                    empleados.area_id, areas.nombre AS area_nombre
             FROM empleados
             JOIN areas ON areas.id = empleados.area_id
             WHERE empleados.activo = 1
             ORDER BY empleados.nombre_completo'
        );

        return $stmt->fetchAll();
    }

    public static function buscarPorId(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT empleados.id, empleados.nombre_completo, empleados.codigo_empleado,
                    empleados.area_id, empleados.activo, areas.nombre AS area_nombre
             FROM empleados
             JOIN areas ON areas.id = empleados.area_id
             WHERE empleados.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $empleado = $stmt->fetch();

        return $empleado === false ? null : $empleado;
    }

    public static function crear(string $nombreCompleto, ?string $codigoEmpleado, int $areaId): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO empleados (nombre_completo, codigo_empleado, area_id)
             VALUES (:nombre_completo, :codigo_empleado, :area_id)'
        );
        $stmt->execute([
            'nombre_completo' => $nombreCompleto,
            'codigo_empleado' => $codigoEmpleado,
            'area_id' => $areaId,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function actualizar(int $id, string $nombreCompleto, ?string $codigoEmpleado, int $areaId): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE empleados
             SET nombre_completo = :nombre_completo, codigo_empleado = :codigo_empleado, area_id = :area_id
             WHERE id = :id'
        );
        $stmt->execute([
            'nombre_completo' => $nombreCompleto,
            'codigo_empleado' => $codigoEmpleado,
            'area_id' => $areaId,
            'id' => $id,
        ]);
    }

    public static function eliminar(int $id): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE empleados SET activo = 0 WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
