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

<table>
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

<p><a class="boton" href="/reportes/por_area_csv.php<?= $areaId ? '?area_id=' . $areaId : '' ?>">Exportar CSV</a></p>

<?php if ($detalle !== null): ?>
<h2>Detalle del área seleccionada</h2>
<table>
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
            <td><?= e($fila['estado']) ?></td>
            <td><?= e($fila['asignado_a'] ?? 'Sin asignar') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($detalle)): ?>
        <tr><td colspan="6">Esta área no tiene equipos.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php endif; ?>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
