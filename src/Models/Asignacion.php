<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

final class Asignacion
{
    /**
     * Asigna un equipo a un empleado. Si el equipo ya tenía una asignación
     * activa, la cierra automáticamente (fecha_devolucion = fecha de la
     * nueva asignación) dentro de la misma transacción.
     */
    public static function asignarEquipo(
        int $equipoId,
        int $empleadoId,
        string $fechaAsignacion,
        ?int $usuarioId,
        ?string $observaciones = null
    ): void {
        $pdo = Database::getConnection();

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare(
                'SELECT id FROM asignaciones
                 WHERE equipo_id = :equipo_id AND fecha_devolucion IS NULL
                 FOR UPDATE'
            );
            $stmt->execute(['equipo_id' => $equipoId]);
            $activaId = $stmt->fetchColumn();

            if ($activaId !== false) {
                $stmt = $pdo->prepare(
                    'UPDATE asignaciones SET fecha_devolucion = :fecha WHERE id = :id'
                );
                $stmt->execute(['fecha' => $fechaAsignacion, 'id' => $activaId]);
            }

            $stmt = $pdo->prepare(
                'INSERT INTO asignaciones (
                    equipo_id, empleado_id, fecha_asignacion, observaciones, creado_por
                 ) VALUES (
                    :equipo_id, :empleado_id, :fecha_asignacion, :observaciones, :creado_por
                 )'
            );
            $stmt->execute([
                'equipo_id' => $equipoId,
                'empleado_id' => $empleadoId,
                'fecha_asignacion' => $fechaAsignacion,
                'observaciones' => $observaciones,
                'creado_por' => $usuarioId,
            ]);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Cierra la asignación activa de un equipo sin crear una nueva.
     */
    public static function devolver(int $asignacionId, string $fechaDevolucion): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE asignaciones
             SET fecha_devolucion = :fecha
             WHERE id = :id AND fecha_devolucion IS NULL'
        );
        $stmt->execute(['fecha' => $fechaDevolucion, 'id' => $asignacionId]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function historialPorEquipo(int $equipoId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT asignaciones.*, empleados.nombre_completo
             FROM asignaciones
             JOIN empleados ON empleados.id = asignaciones.empleado_id
             WHERE asignaciones.equipo_id = :equipo_id
             ORDER BY asignaciones.fecha_asignacion DESC, asignaciones.id DESC'
        );
        $stmt->execute(['equipo_id' => $equipoId]);

        return $stmt->fetchAll();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function historialPorEmpleado(int $empleadoId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT asignaciones.*, equipos.marca, equipos.modelo, equipos.numero_serie
             FROM asignaciones
             JOIN equipos ON equipos.id = asignaciones.equipo_id
             WHERE asignaciones.empleado_id = :empleado_id
             ORDER BY asignaciones.fecha_asignacion DESC, asignaciones.id DESC'
        );
        $stmt->execute(['empleado_id' => $empleadoId]);

        return $stmt->fetchAll();
    }

    public static function buscarPorId(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM asignaciones WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $asignacion = $stmt->fetch();

        return $asignacion === false ? null : $asignacion;
    }
}
