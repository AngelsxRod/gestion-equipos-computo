<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

final class Equipo
{
    /**
     * @param array{tipo?: string, estado?: string, area_id?: int} $filtros
     * @return array<int, array<string, mixed>>
     */
    public static function listar(array $filtros = []): array
    {
        $pdo = Database::getConnection();

        $condiciones = ['equipos.activo = 1'];
        $parametros = [];

        if (!empty($filtros['tipo'])) {
            $condiciones[] = 'equipos.tipo = :tipo';
            $parametros['tipo'] = $filtros['tipo'];
        }
        if (!empty($filtros['estado'])) {
            $condiciones[] = 'equipos.estado = :estado';
            $parametros['estado'] = $filtros['estado'];
        }
        if (!empty($filtros['area_id'])) {
            $condiciones[] = 'equipos.area_id = :area_id';
            $parametros['area_id'] = $filtros['area_id'];
        }

        $sql = 'SELECT equipos.*, areas.nombre AS area_nombre,
                       empleados.nombre_completo AS asignado_a
                FROM equipos
                LEFT JOIN areas ON areas.id = equipos.area_id
                LEFT JOIN asignaciones ON asignaciones.equipo_id = equipos.id
                       AND asignaciones.fecha_devolucion IS NULL
                LEFT JOIN empleados ON empleados.id = asignaciones.empleado_id
                WHERE ' . implode(' AND ', $condiciones) . '
                ORDER BY equipos.marca, equipos.modelo';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll();
    }

    public static function buscarPorId(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT equipos.*, areas.nombre AS area_nombre
             FROM equipos
             LEFT JOIN areas ON areas.id = equipos.area_id
             WHERE equipos.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $equipo = $stmt->fetch();

        return $equipo === false ? null : $equipo;
    }

    public static function asignacionActiva(int $equipoId): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT asignaciones.id, asignaciones.fecha_asignacion,
                    empleados.id AS empleado_id, empleados.nombre_completo
             FROM asignaciones
             JOIN empleados ON empleados.id = asignaciones.empleado_id
             WHERE asignaciones.equipo_id = :equipo_id
               AND asignaciones.fecha_devolucion IS NULL'
        );
        $stmt->execute(['equipo_id' => $equipoId]);
        $asignacion = $stmt->fetch();

        return $asignacion === false ? null : $asignacion;
    }

    /**
     * @param array{
     *   tipo: string, marca: string, modelo: string, numero_serie: string,
     *   anio_adquisicion: int, estado: string, garantia_proveedor: ?string,
     *   garantia_inicio: ?string, garantia_fin: ?string, area_id: ?int
     * } $datos
     */
    public static function crear(array $datos): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO equipos (
                tipo, marca, modelo, numero_serie, anio_adquisicion, estado,
                garantia_proveedor, garantia_inicio, garantia_fin, area_id
             ) VALUES (
                :tipo, :marca, :modelo, :numero_serie, :anio_adquisicion, :estado,
                :garantia_proveedor, :garantia_inicio, :garantia_fin, :area_id
             )'
        );
        $stmt->execute($datos);

        return (int) $pdo->lastInsertId();
    }

    /**
     * @param array{
     *   tipo: string, marca: string, modelo: string, numero_serie: string,
     *   anio_adquisicion: int, estado: string, garantia_proveedor: ?string,
     *   garantia_inicio: ?string, garantia_fin: ?string, area_id: ?int
     * } $datos
     */
    public static function actualizar(int $id, array $datos): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE equipos SET
                tipo = :tipo, marca = :marca, modelo = :modelo, numero_serie = :numero_serie,
                anio_adquisicion = :anio_adquisicion, estado = :estado,
                garantia_proveedor = :garantia_proveedor, garantia_inicio = :garantia_inicio,
                garantia_fin = :garantia_fin, area_id = :area_id
             WHERE id = :id'
        );
        $stmt->execute($datos + ['id' => $id]);
    }

    /**
     * Soft delete. Devuelve false si el equipo tiene una asignación activa.
     */
    public static function eliminar(int $id): bool
    {
        if (self::asignacionActiva($id) !== null) {
            return false;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE equipos SET activo = 0 WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return true;
    }

    /**
     * Equipos activos disponibles para elegir en el formulario de asignación.
     * @return array<int, array<string, mixed>>
     */
    public static function listarParaAsignar(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query(
            "SELECT id, marca, modelo, numero_serie
             FROM equipos
             WHERE activo = 1 AND estado = 'activo'
             ORDER BY marca, modelo"
        );

        return $stmt->fetchAll();
    }
}
