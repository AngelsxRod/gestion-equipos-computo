<?php

declare(strict_types=1);

namespace App\Reports;

use App\Config\Database;

// "Año" = año de ADQUISICIÓN del equipo (equipos.anio_adquisicion), no el
// año en que se asignó a la persona que lo tiene actualmente. Si en el
// futuro se necesita el otro sentido, cambiar a YEAR(asignaciones.fecha_asignacion)
// sobre la asignación activa.
final class ReporteAnio
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function resumenPorAnio(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query(
            'SELECT anio_adquisicion, COUNT(*) AS total_equipos
             FROM equipos
             WHERE activo = 1
             GROUP BY anio_adquisicion
             ORDER BY anio_adquisicion DESC'
        );

        return $stmt->fetchAll();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function equiposDeAnio(int $anio): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT equipos.tipo, equipos.marca, equipos.modelo, equipos.numero_serie,
                    equipos.estado, areas.nombre AS area_nombre,
                    empleados.nombre_completo AS asignado_a
             FROM equipos
             LEFT JOIN areas ON areas.id = equipos.area_id
             LEFT JOIN asignaciones ON asignaciones.equipo_id = equipos.id
                    AND asignaciones.fecha_devolucion IS NULL
             LEFT JOIN empleados ON empleados.id = asignaciones.empleado_id
             WHERE equipos.anio_adquisicion = :anio AND equipos.activo = 1
             ORDER BY equipos.marca, equipos.modelo'
        );
        $stmt->execute(['anio' => $anio]);

        return $stmt->fetchAll();
    }
}
