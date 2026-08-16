<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Auth\Csrf;
use App\Models\Area;
use App\Models\Empleado;

require_role(['admin']);

$areas = Area::listarActivas();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validar($_POST['csrf_token'] ?? null)) {
        $error = 'Sesión expirada, intenta de nuevo.';
    } else {
        $nombreCompleto = trim($_POST['nombre_completo'] ?? '');
        $codigoEmpleado = trim($_POST['codigo_empleado'] ?? '') ?: null;
        $areaId = (int) ($_POST['area_id'] ?? 0);

        if ($nombreCompleto === '' || $areaId === 0) {
            $error = 'Nombre y área son obligatorios.';
        } else {
            Empleado::crear($nombreCompleto, $codigoEmpleado, $areaId);
            flash_set('exito', 'Empleado creado correctamente.');
            redirect('/empleados/listar.php');
        }
    }
}

$titulo = 'Nuevo empleado';
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1>Nuevo empleado</h1>
<?php if ($error): ?><div class="flash-error"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="card max-w-lg">
    <?= Csrf::campoHtml() ?>
    <label for="nombre_completo">Nombre completo</label>
    <input type="text" id="nombre_completo" name="nombre_completo" value="<?= e($_POST['nombre_completo'] ?? '') ?>" required autofocus>

    <label for="codigo_empleado">Código de empleado (opcional)</label>
    <input type="text" id="codigo_empleado" name="codigo_empleado" value="<?= e($_POST['codigo_empleado'] ?? '') ?>">

    <label for="area_id">Área</label>
    <select id="area_id" name="area_id" required>
        <option value="">-- Selecciona --</option>
        <?php foreach ($areas as $area): ?>
        <option value="<?= (int) $area['id'] ?>"><?= e($area['nombre']) ?></option>
        <?php endforeach; ?>
    </select>

    <div class="mt-6 flex gap-2">
        <button type="submit" class="btn-primary">Guardar</button>
        <a class="btn-secondary" href="/empleados/listar.php">Cancelar</a>
    </div>
</form>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
