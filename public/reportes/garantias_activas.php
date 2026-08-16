<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Models\Area;
use App\Reports\ReporteGarantias;

require_role(['admin', 'consulta']);

$areas = Area::listarActivas();
$filtros = [
    'vence_desde' => $_GET['vence_desde'] ?? '',
    'vence_hasta' => $_GET['vence_hasta'] ?? '',
    'area_id' => $_GET['area_id'] ?? '',
];
$equipos = ReporteGarantias::garantiasActivas(array_filter($filtros));

$titulo = 'Garantías activas';
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1>Equipos con garantía activa</h1>

<form method="get" class="filtros">
    <div>
        <label for="vence_desde">Vence desde</label>
        <input type="date" id="vence_desde" name="vence_desde" value="<?= e($filtros['vence_desde']) ?>">
    </div>
    <div>
        <label for="vence_hasta">Vence hasta</label>
        <input type="date" id="vence_hasta" name="vence_hasta" value="<?= e($filtros['vence_hasta']) ?>">
    </div>
    <div>
        <label for="area_id">Área</label>
        <select id="area_id" name="area_id">
            <option value="">Todas</option>
            <?php foreach ($areas as $area): ?>
            <option value="<?= (int) $area['id'] ?>" <?= (string) $area['id'] === (string) $filtros['area_id'] ? 'selected' : '' ?>><?= e($area['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div><button type="submit">Filtrar</button></div>
</form>

<p>
    <a class="boton" href="/reportes/garantias_activas_csv.php?<?= http_build_query($filtros) ?>">Exportar CSV</a>
</p>

<table>
    <thead>
        <tr><th>Equipo</th><th>Serie</th><th>Área</th><th>Proveedor</th><th>Vence</th><th>Días restantes</th></tr>
    </thead>
    <tbody>
        <?php foreach ($equipos as $equipo): ?>
        <tr>
            <td><?= e($equipo['marca']) ?> <?= e($equipo['modelo']) ?></td>
            <td><?= e($equipo['numero_serie']) ?></td>
            <td><?= e($equipo['area_nombre'] ?? '') ?></td>
            <td><?= e($equipo['garantia_proveedor'] ?? '') ?></td>
            <td><?= e($equipo['garantia_fin']) ?></td>
            <td><?= (int) $equipo['dias_restantes'] ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($equipos)): ?>
        <tr><td colspan="6">No hay equipos con garantía activa que coincidan con el filtro.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
