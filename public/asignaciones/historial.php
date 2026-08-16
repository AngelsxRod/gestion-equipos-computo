<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Models\Asignacion;
use App\Models\Empleado;
use App\Models\Equipo;

require_role(['admin', 'consulta']);

$equipoId = (int) ($_GET['equipo_id'] ?? 0);
$empleadoId = (int) ($_GET['empleado_id'] ?? 0);

if ($equipoId > 0) {
    $sujeto = Equipo::buscarPorId($equipoId);
    if ($sujeto === null) {
        flash_set('error', 'Equipo no encontrado.');
        redirect('/equipos/listar.php');
    }
    $historial = Asignacion::historialPorEquipo($equipoId);
    $encabezado = 'Historial de: ' . $sujeto['marca'] . ' ' . $sujeto['modelo'];
    $tipoVista = 'equipo';
} elseif ($empleadoId > 0) {
    $sujeto = Empleado::buscarPorId($empleadoId);
    if ($sujeto === null) {
        flash_set('error', 'Empleado no encontrado.');
        redirect('/empleados/listar.php');
    }
    $historial = Asignacion::historialPorEmpleado($empleadoId);
    $encabezado = 'Historial de: ' . $sujeto['nombre_completo'];
    $tipoVista = 'empleado';
} else {
    flash_set('error', 'Debes indicar un equipo o un empleado.');
    redirect('/dashboard.php');
}

$titulo = $encabezado;
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1><?= e($encabezado) ?></h1>
<table>
    <thead>
        <tr>
            <?php if ($tipoVista === 'equipo'): ?>
                <th>Empleado</th>
            <?php else: ?>
                <th>Equipo</th>
            <?php endif; ?>
            <th>Fecha asignación</th>
            <th>Fecha devolución</th>
            <th>Observaciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($historial as $fila): ?>
        <tr>
            <?php if ($tipoVista === 'equipo'): ?>
                <td><?= e($fila['nombre_completo']) ?></td>
            <?php else: ?>
                <td><?= e($fila['marca']) ?> <?= e($fila['modelo']) ?> (<?= e($fila['numero_serie']) ?>)</td>
            <?php endif; ?>
            <td><?= e($fila['fecha_asignacion']) ?></td>
            <td><?= e($fila['fecha_devolucion'] ?? 'Activa') ?></td>
            <td><?= e($fila['observaciones'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($historial)): ?>
        <tr><td colspan="4">No hay historial de asignaciones.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
