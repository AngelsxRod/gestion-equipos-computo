<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Models\Empleado;

require_role(['admin', 'consulta']);

$esAdmin = usuario_actual()['rol'] === 'admin';
$empleados = Empleado::listarActivos();

$titulo = 'Empleados';
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1>Empleados</h1>
<?php if ($esAdmin): ?>
    <p><a class="boton" href="/empleados/crear.php">+ Nuevo empleado</a></p>
<?php endif; ?>
<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Código</th>
            <th>Área</th>
            <th>Historial</th>
            <?php if ($esAdmin): ?><th>Acciones</th><?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($empleados as $empleado): ?>
        <tr>
            <td><?= e($empleado['nombre_completo']) ?></td>
            <td><?= e($empleado['codigo_empleado'] ?? '') ?></td>
            <td><?= e($empleado['area_nombre']) ?></td>
            <td><a href="/asignaciones/historial.php?empleado_id=<?= (int) $empleado['id'] ?>">Ver</a></td>
            <?php if ($esAdmin): ?>
            <td>
                <a href="/empleados/editar.php?id=<?= (int) $empleado['id'] ?>">Editar</a>
                &nbsp;
                <a href="/empleados/eliminar.php?id=<?= (int) $empleado['id'] ?>">Eliminar</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($empleados)): ?>
        <tr><td colspan="5">No hay empleados registrados.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
