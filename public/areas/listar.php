<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Models\Area;

require_role(['admin', 'consulta']);

$esAdmin = usuario_actual()['rol'] === 'admin';
$areas = Area::listarActivas();

$titulo = 'Áreas';
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1>Áreas</h1>
<?php if ($esAdmin): ?>
    <p class="mb-4"><a class="btn-primary" href="/areas/crear.php">+ Nueva área</a></p>
<?php endif; ?>
<div class="table-wrap">
<table class="table-base">
    <thead>
        <tr>
            <th>Nombre</th>
            <?php if ($esAdmin): ?><th>Acciones</th><?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($areas as $area): ?>
        <tr>
            <td><?= e($area['nombre']) ?></td>
            <?php if ($esAdmin): ?>
            <td>
                <a href="/areas/editar.php?id=<?= (int) $area['id'] ?>">Editar</a>
                &nbsp;
                <a href="/areas/eliminar.php?id=<?= (int) $area['id'] ?>">Eliminar</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($areas)): ?>
        <tr><td colspan="2">No hay áreas registradas.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</div>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
