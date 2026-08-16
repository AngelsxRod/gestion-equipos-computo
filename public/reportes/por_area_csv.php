<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Helpers\Csv;
use App\Reports\ReporteAreas;

require_role(['admin', 'consulta']);

$areaId = (int) ($_GET['area_id'] ?? 0);

if ($areaId > 0) {
    $filas = array_map(
        fn (array $f) => [$f['tipo'], $f['marca'], $f['modelo'], $f['numero_serie'], $f['anio_adquisicion'], $f['estado'], $f['asignado_a'] ?? 'Sin asignar'],
        ReporteAreas::equiposDeArea($areaId)
    );
    Csv::exportar($filas, ['Tipo', 'Marca', 'Modelo', 'Serie', 'Año', 'Estado', 'Asignado a'], 'equipos_area.csv');
}

$filas = array_map(
    fn (array $f) => [$f['nombre'], $f['total_equipos']],
    ReporteAreas::resumenPorArea()
);
Csv::exportar($filas, ['Área', 'Total equipos'], 'equipos_por_area.csv');
