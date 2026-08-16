<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Reports\ReporteAnio;

require_role(['admin', 'consulta']);

$anio = (int) ($_GET['anio'] ?? 0);
$resumen = ReporteAnio::resumenPorAnio();
$detalle = $anio > 0 ? ReporteAnio::equiposDeAnio($anio) : null;

$titulo = 'Reporte por año de adquisición';
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1>Reporte de equipos por año de adquisición</h1>

<table>
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

<p><a class="boton" href="/reportes/por_anio_csv.php<?= $anio ? '?anio=' . $anio : '' ?>">Exportar CSV</a></p>

<?php if ($detalle !== null): ?>
<h2>Equipos adquiridos en <?= (int) $anio ?></h2>
<table>
    <thead>
        <tr><th>Tipo</th><th>Marca / Modelo</th><th>Serie</th><th>Estado</th><th>Área</th><th>Asignado a</th></tr>
    </thead>
    <tbody>
        <?php foreach ($detalle as $fila): ?>
        <tr>
            <td><?= e($fila['tipo']) ?></td>
            <td><?= e($fila['marca']) ?> <?= e($fila['modelo']) ?></td>
            <td><?= e($fila['numero_serie']) ?></td>
            <td><?= e($fila['estado']) ?></td>
            <td><?= e($fila['area_nombre'] ?? '') ?></td>
            <td><?= e($fila['asignado_a'] ?? 'Sin asignar') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($detalle)): ?>
        <tr><td colspan="6">No hay equipos adquiridos en este año.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php endif; ?>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
