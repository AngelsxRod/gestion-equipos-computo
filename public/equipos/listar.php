<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Models\Area;
use App\Models\Equipo;

require_role(['admin', 'consulta']);

$esAdmin = usuario_actual()['rol'] === 'admin';
$areas = Area::listarActivas();

$filtros = [
    'tipo' => $_GET['tipo'] ?? '',
    'estado' => $_GET['estado'] ?? '',
    'area_id' => $_GET['area_id'] ?? '',
];
$equipos = Equipo::listar(array_filter($filtros));

$titulo = 'Equipos';
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1>Equipos</h1>
<?php if ($esAdmin): ?>
    <p><a class="boton" href="/equipos/crear.php">+ Nuevo equipo</a></p>
<?php endif; ?>

<form method="get" class="filtros">
    <div>
        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo">
            <option value="">Todos</option>
            <option value="laptop" <?= $filtros['tipo'] === 'laptop' ? 'selected' : '' ?>>Laptop</option>
            <option value="escritorio" <?= $filtros['tipo'] === 'escritorio' ? 'selected' : '' ?>>Escritorio</option>
        </select>
    </div>
    <div>
        <label for="estado">Estado</label>
        <select id="estado" name="estado">
            <option value="">Todos</option>
            <option value="activo" <?= $filtros['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="en_reparacion" <?= $filtros['estado'] === 'en_reparacion' ? 'selected' : '' ?>>En reparación</option>
            <option value="de_baja" <?= $filtros['estado'] === 'de_baja' ? 'selected' : '' ?>>De baja</option>
        </select>
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

<table>
    <thead>
        <tr>
            <th>Tipo</th>
            <th>Marca / Modelo</th>
            <th>Serie</th>
            <th>Año</th>
            <th>Estado</th>
            <th>Área</th>
            <th>Asignado a</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($equipos as $equipo): ?>
        <tr>
            <td><?= e($equipo['tipo']) ?></td>
            <td><?= e($equipo['marca']) ?> <?= e($equipo['modelo']) ?></td>
            <td><?= e($equipo['numero_serie']) ?></td>
            <td><?= (int) $equipo['anio_adquisicion'] ?></td>
            <td><?= e($equipo['estado']) ?></td>
            <td><?= e($equipo['area_nombre'] ?? '') ?></td>
            <td><?= e($equipo['asignado_a'] ?? 'Sin asignar') ?></td>
            <td>
                <a href="/equipos/ver.php?id=<?= (int) $equipo['id'] ?>">Ver</a>
                <?php if ($esAdmin): ?>
                &nbsp;<a href="/equipos/editar.php?id=<?= (int) $equipo['id'] ?>">Editar</a>
                &nbsp;<a href="/equipos/eliminar.php?id=<?= (int) $equipo['id'] ?>">Eliminar</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($equipos)): ?>
        <tr><td colspan="8">No hay equipos que coincidan con el filtro.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
