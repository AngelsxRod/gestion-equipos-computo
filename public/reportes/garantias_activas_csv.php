<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Helpers\Csv;
use App\Reports\ReporteGarantias;

require_role(['admin', 'consulta']);

$filtros = [
    'vence_desde' => $_GET['vence_desde'] ?? '',
    'vence_hasta' => $_GET['vence_hasta'] ?? '',
    'area_id' => $_GET['area_id'] ?? '',
];

$filas = array_map(
    fn (array $f) => [
        $f['marca'] . ' ' . $f['modelo'],
        $f['numero_serie'],
        $f['area_nombre'] ?? '',
        $f['garantia_proveedor'] ?? '',
        $f['garantia_fin'],
        $f['dias_restantes'],
    ],
    ReporteGarantias::garantiasActivas(array_filter($filtros))
);

Csv::exportar($filas, ['Equipo', 'Serie', 'Área', 'Proveedor', 'Vence', 'Días restantes'], 'garantias_activas.csv');
