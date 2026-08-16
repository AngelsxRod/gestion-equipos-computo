<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Reports\ReporteAnio;

require_role(['admin', 'consulta']);

$anio = (int) ($_GET['anio'] ?? 0);
$resumen = ReporteAnio::resumenPorAnio();
$detalle = $anio > 0 ? ReporteAnio::equiposDeAnio($anio) : null;

$resumenAsc = $resumen;
usort($resumenAsc, fn (array $a, array $b) => $a['anio_adquisicion'] <=> $b['anio_adquisicion']);

$titulo = 'Reporte por año de adquisición';
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1>Reporte de equipos por año de adquisición</h1>

<div class="card mb-6">
    <h2 class="mt-0">Equipos por año de adquisición</h2>
    <canvas id="chartAnios" height="90"></canvas>
</div>

<div class="table-wrap">
<table class="table-base">
    <thead><tr><th>Año de adquisición</th><th>Total equipos</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($resumen as $fila): ?>
        <tr>
            <td><?= (int) $fila['anio_adquisicion'] ?></td>
            <td><?= (int) $fila['total_equipos'] ?></td>
            <td><a href="/reportes/por_anio.php?anio=<?= (int) $fila['anio_adquisicion'] ?>">Ver detalle</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<p><a class="btn-primary" href="/reportes/por_anio_csv.php<?= $anio ? '?anio=' . $anio : '' ?>">Exportar CSV</a></p>

<?php if ($detalle !== null): ?>
<h2>Equipos adquiridos en <?= (int) $anio ?></h2>
<div class="table-wrap">
<table class="table-base">
    <thead>
        <tr><th>Tipo</th><th>Marca / Modelo</th><th>Serie</th><th>Estado</th><th>Área</th><th>Asignado a</th></tr>
    </thead>
    <tbody>
        <?php foreach ($detalle as $fila): ?>
        <tr>
            <td><?= e($fila['tipo']) ?></td>
            <td><?= e($fila['marca']) ?> <?= e($fila['modelo']) ?></td>
            <td><?= e($fila['numero_serie']) ?></td>
            <td><?= badge_estado_equipo($fila['estado']) ?></td>
            <td><?= e($fila['area_nombre'] ?? '') ?></td>
            <td><?= e($fila['asignado_a'] ?? 'Sin asignar') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($detalle)): ?>
        <tr><td colspan="6">No hay equipos adquiridos en este año.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</div>
<?php endif; ?>

<script src="/assets/js/chart.min.js"></script>
<script>
const datosAnios = <?= json_encode([
    'labels' => array_map('strval', array_column($resumenAsc, 'anio_adquisicion')),
    'valores' => array_map('intval', array_column($resumenAsc, 'total_equipos')),
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

new Chart(document.getElementById('chartAnios'), {
    type: 'bar',
    data: {
        labels: datosAnios.labels,
        datasets: [{ label: 'Equipos adquiridos', data: datosAnios.valores, backgroundColor: '#0f766e', borderRadius: 4 }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
});
</script>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
