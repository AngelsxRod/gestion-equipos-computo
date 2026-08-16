<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Helpers\Csv;
use App\Reports\ReporteAnio;

require_role(['admin', 'consulta']);

$anio = (int) ($_GET['anio'] ?? 0);

if ($anio > 0) {
    $filas = array_map(
        fn (array $f) => [$f['tipo'], $f['marca'], $f['modelo'], $f['numero_serie'], $f['estado'], $f['area_nombre'] ?? '', $f['asignado_a'] ?? 'Sin asignar'],
        ReporteAnio::equiposDeAnio($anio)
    );
    Csv::exportar($filas, ['Tipo', 'Marca', 'Modelo', 'Serie', 'Estado', 'Área', 'Asignado a'], 'equipos_anio.csv');
}

$filas = array_map(
    fn (array $f) => [$f['anio_adquisicion'], $f['total_equipos']],
    ReporteAnio::resumenPorAnio()
);
Csv::exportar($filas, ['Año de adquisición', 'Total equipos'], 'equipos_por_anio.csv');
