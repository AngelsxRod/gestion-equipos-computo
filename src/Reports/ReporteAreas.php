<?php

declare(strict_types=1);

namespace App\Reports;

use App\Config\Database;

final class ReporteAreas
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function resumenPorArea(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query(
            'SELECT areas.id, areas.nombre, COUNT(equipos.id) AS total_equipos
             FROM areas
             LEFT JOIN equipos ON equipos.area_id = areas.id AND equipos.activo = 1
             WHERE areas.activo = 1
             GROUP BY areas.id, areas.nombre
             ORDER BY areas.nombre'
        );

        return $stmt->fetchAll();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function equiposDeArea(int $areaId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT equipos.tipo, equipos.marca, equipos.modelo, equipos.numero_serie,
                    equipos.anio_adquisicion, equipos.estado,
                    empleados.nombre_completo AS asignado_a
             FROM equipos
             LEFT JOIN asignaciones ON asignaciones.equipo_id = equipos.id
                    AND asignaciones.fecha_devolucion IS NULL
             LEFT JOIN empleados ON empleados.id = asignaciones.empleado_id
             WHERE equipos.area_id = :area_id AND equipos.activo = 1
             ORDER BY equipos.marca, equipos.modelo'
        );
        $stmt->execute(['area_id' => $areaId]);

        return $stmt->fetchAll();
    }
}
