<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Models\Asignacion;
use App\Models\Equipo;

require_role(['admin', 'consulta']);

$id = (int) ($_GET['id'] ?? 0);
$equipo = Equipo::buscarPorId($id);

if ($equipo === null) {
    flash_set('error', 'Equipo no encontrado.');
    redirect('/equipos/listar.php');
}

$esAdmin = usuario_actual()['rol'] === 'admin';
$asignacionActiva = Equipo::asignacionActiva($id);
$historial = Asignacion::historialPorEquipo($id);

$titulo = 'Equipo: ' . $equipo['marca'] . ' ' . $equipo['modelo'];
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1><?= e($equipo['marca']) ?> <?= e($equipo['modelo']) ?></h1>

<div class="table-wrap">
<table class="table-base">
    <tr><th>Tipo</th><td><?= e($equipo['tipo']) ?></td></tr>
    <tr><th>Número de serie</th><td><?= e($equipo['numero_serie']) ?></td></tr>
    <tr><th>Año de adquisición</th><td><?= (int) $equipo['anio_adquisicion'] ?></td></tr>
    <tr><th>Estado</th><td><?= badge_estado_equipo($equipo['estado']) ?></td></tr>
    <tr><th>Área</th><td><?= e($equipo['area_nombre'] ?? 'Sin asignar') ?></td></tr>
    <tr><th>Garantía</th>
        <td>
            <?php if ($equipo['garantia_fin']): ?>
                <?= e($equipo['garantia_proveedor'] ?? '') ?>
                (<?= e($equipo['garantia_inicio'] ?? '?') ?> a <?= e($equipo['garantia_fin']) ?>)
            <?php else: ?>
                Sin datos de garantía
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <th>Asignación activa</th>
        <td>
            <?php if ($asignacionActiva): ?>
                <?= e($asignacionActiva['nombre_completo']) ?> (desde <?= e($asignacionActiva['fecha_asignacion']) ?>)
            <?php else: ?>
                Sin asignar
            <?php endif; ?>
        </td>
    </tr>
</table>
</div>

<?php if ($esAdmin): ?>
<div class="mt-6 flex gap-2">
    <a class="btn-primary" href="/asignaciones/asignar.php?equipo_id=<?= (int) $equipo['id'] ?>">
        <?= $asignacionActiva ? 'Reasignar' : 'Asignar' ?>
    </a>
    <?php if ($asignacionActiva): ?>
    <a class="btn-secondary" href="/asignaciones/devolver.php?asignacion_id=<?= (int) $asignacionActiva['id'] ?>">Devolver</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<h2>Historial de asignaciones</h2>
<div class="table-wrap">
<table class="table-base">
    <thead>
        <tr>
            <th>Empleado</th>
            <th>Fecha asignación</th>
            <th>Fecha devolución</th>
            <th>Observaciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($historial as $fila): ?>
        <tr>
            <td><?= e($fila['nombre_completo']) ?></td>
            <td><?= e($fila['fecha_asignacion']) ?></td>
            <td><?= e($fila['fecha_devolucion'] ?? 'Activa') ?></td>
            <td><?= e($fila['observaciones'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($historial)): ?>
        <tr><td colspan="4">Este equipo no tiene historial de asignaciones.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</div>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
