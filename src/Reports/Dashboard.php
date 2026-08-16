<?php

declare(strict_types=1);

namespace App\Reports;

use App\Config\Database;

final class Dashboard
{
    /**
     * @return array<string, int>
     */
    public static function kpis(): array
    {
        $pdo = Database::getConnection();

        $equiposActivos = (int) $pdo->query(
            "SELECT COUNT(*) FROM equipos WHERE activo = 1 AND estado = 'activo'"
        )->fetchColumn();

        $enReparacion = (int) $pdo->query(
            "SELECT COUNT(*) FROM equipos WHERE activo = 1 AND estado = 'en_reparacion'"
        )->fetchColumn();

        $garantiaVigente = (int) $pdo->query(
            'SELECT COUNT(*) FROM equipos
             WHERE activo = 1 AND garantia_fin IS NOT NULL AND garantia_fin >= CURDATE()'
        )->fetchColumn();

        $sinAsignar = (int) $pdo->query(
            "SELECT COUNT(*) FROM equipos e
             LEFT JOIN asignaciones a ON a.equipo_id = e.id AND a.fecha_devolucion IS NULL
             WHERE e.activo = 1 AND e.estado = 'activo' AND a.id IS NULL"
        )->fetchColumn();

        $totalEmpleados = (int) $pdo->query(
            'SELECT COUNT(*) FROM empleados WHERE activo = 1'
        )->fetchColumn();

        $totalAreas = (int) $pdo->query(
            'SELECT COUNT(*) FROM areas WHERE activo = 1'
        )->fetchColumn();

        return [
            'equipos_activos' => $equiposActivos,
            'equipos_en_reparacion' => $enReparacion,
            'equipos_con_garantia_vigente' => $garantiaVigente,
            'equipos_sin_asignar' => $sinAsignar,
            'total_empleados' => $totalEmpleados,
            'total_areas' => $totalAreas,
        ];
    }
}
