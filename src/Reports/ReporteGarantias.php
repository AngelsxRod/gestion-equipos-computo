<?php

declare(strict_types=1);

namespace App\Reports;

use App\Config\Database;

final class ReporteGarantias
{
    /**
     * @param array{vence_desde?: string, vence_hasta?: string, area_id?: int} $filtros
     * @return array{0: string[], 1: array<string, mixed>}
     */
    private static function construirCondiciones(array $filtros): array
    {
        $condiciones = [
            'equipos.activo = 1',
            'equipos.garantia_fin IS NOT NULL',
            'equipos.garantia_fin >= CURDATE()',
        ];
        $parametros = [];

        if (!empty($filtros['vence_desde'])) {
            $condiciones[] = 'equipos.garantia_fin >= :vence_desde';
            $parametros['vence_desde'] = $filtros['vence_desde'];
        }
        if (!empty($filtros['vence_hasta'])) {
            $condiciones[] = 'equipos.garantia_fin <= :vence_hasta';
            $parametros['vence_hasta'] = $filtros['vence_hasta'];
        }
        if (!empty($filtros['area_id'])) {
            $condiciones[] = 'equipos.area_id = :area_id';
            $parametros['area_id'] = $filtros['area_id'];
        }

        return [$condiciones, $parametros];
    }

    /**
     * @param array{vence_desde?: string, vence_hasta?: string, area_id?: int} $filtros
     * @return array<int, array<string, mixed>>
     */
    public static function garantiasActivas(array $filtros = []): array
    {
        $pdo = Database::getConnection();
        [$condiciones, $parametros] = self::construirCondiciones($filtros);

        $sql = 'SELECT equipos.id, equipos.tipo, equipos.marca, equipos.modelo, equipos.numero_serie,
                       equipos.garantia_proveedor, equipos.garantia_inicio, equipos.garantia_fin,
                       DATEDIFF(equipos.garantia_fin, CURDATE()) AS dias_restantes,
                       areas.nombre AS area_nombre
                FROM equipos
                LEFT JOIN areas ON areas.id = equipos.area_id
                WHERE ' . implode(' AND ', $condiciones) . '
                ORDER BY equipos.garantia_fin ASC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll();
    }

    /**
     * @param array{vence_desde?: string, vence_hasta?: string, area_id?: int} $filtros
     * @return array<int, array{mes: string, total: int}>
     */
    public static function vencimientosPorMes(array $filtros = []): array
    {
        $pdo = Database::getConnection();
        [$condiciones, $parametros] = self::construirCondiciones($filtros);

        $sql = 'SELECT DATE_FORMAT(equipos.garantia_fin, "%Y-%m") AS mes, COUNT(*) AS total
                FROM equipos
                LEFT JOIN areas ON areas.id = equipos.area_id
                WHERE ' . implode(' AND ', $condiciones) . '
                GROUP BY mes
                ORDER BY mes ASC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll();
    }
}
