<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Reports\ReporteAreas;

require_role(['admin', 'consulta']);

$areaId = (int) ($_GET['area_id'] ?? 0);
$resumen = ReporteAreas::resumenPorArea();
$detalle = $areaId > 0 ? ReporteAreas::equiposDeArea($areaId) : null;

$titulo = 'Reporte por área';
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1>Reporte de equipos por área</h1>

<div class="card mb-6">
    <h2 class="mt-0">Equipos por área</h2>
    <canvas id="chartAreas" height="90"></canvas>
</div>

<div class="table-wrap">
<table class="table-base">
    <thead><tr><th>Área</th><th>Total equipos</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($resumen as $fila): ?>
        <tr>
            <td><?= e($fila['nombre']) ?></td>
            <td><?= (int) $fila['total_equipos'] ?></td>
            <td><a href="/reportes/por_area.php?area_id=<?= (int) $fila['id'] ?>">Ver detalle</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<p><a class="btn-primary" href="/reportes/por_area_csv.php<?= $areaId ? '?area_id=' . $areaId : '' ?>">Exportar CSV</a></p>

<?php if ($detalle !== null): ?>
<h2>Detalle del área seleccionada</h2>
<div class="table-wrap">
<table class="table-base">
    <thead>
        <tr><th>Tipo</th><th>Marca / Modelo</th><th>Serie</th><th>Año</th><th>Estado</th><th>Asignado a</th></tr>
    </thead>
    <tbody>
        <?php foreach ($detalle as $fila): ?>
        <tr>
            <td><?= e($fila['tipo']) ?></td>
            <td><?= e($fila['marca']) ?> <?= e($fila['modelo']) ?></td>
            <td><?= e($fila['numero_serie']) ?></td>
            <td><?= (int) $fila['anio_adquisicion'] ?></td>
            <td><?= badge_estado_equipo($fila['estado']) ?></td>
            <td><?= e($fila['asignado_a'] ?? 'Sin asignar') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($detalle)): ?>
        <tr><td colspan="6">Esta área no tiene equipos.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</div>
<?php endif; ?>

<script src="/assets/js/chart.min.js"></script>
<script>
const datosAreas = <?= json_encode([
    'labels' => array_column($resumen, 'nombre'),
    'valores' => array_map('intval', array_column($resumen, 'total_equipos')),
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

new Chart(document.getElementById('chartAreas'), {
    type: 'bar',
    data: {
        labels: datosAreas.labels,
        datasets: [{ label: 'Equipos', data: datosAreas.valores, backgroundColor: '#2563eb', borderRadius: 4 }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
});
</script>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
