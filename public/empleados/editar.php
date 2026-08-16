<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Auth\Csrf;
use App\Models\Area;
use App\Models\Empleado;

require_role(['admin']);

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$empleado = Empleado::buscarPorId($id);

if ($empleado === null) {
    flash_set('error', 'Empleado no encontrado.');
    redirect('/empleados/listar.php');
}

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
            Empleado::actualizar($id, $nombreCompleto, $codigoEmpleado, $areaId);
            flash_set('exito', 'Empleado actualizado correctamente.');
            redirect('/empleados/listar.php');
        }
        $empleado['nombre_completo'] = $nombreCompleto;
        $empleado['codigo_empleado'] = $codigoEmpleado;
        $empleado['area_id'] = $areaId;
    }
}

$titulo = 'Editar empleado';
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1>Editar empleado</h1>
<?php if ($error): ?><div class="flash error"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="formulario">
    <?= Csrf::campoHtml() ?>
    <input type="hidden" name="id" value="<?= (int) $empleado['id'] ?>">
    <label for="nombre_completo">Nombre completo</label>
    <input type="text" id="nombre_completo" name="nombre_completo" value="<?= e($empleado['nombre_completo']) ?>" required autofocus>

    <label for="codigo_empleado">Código de empleado (opcional)</label>
    <input type="text" id="codigo_empleado" name="codigo_empleado" value="<?= e($empleado['codigo_empleado'] ?? '') ?>">

    <label for="area_id">Área</label>
    <select id="area_id" name="area_id" required>
        <?php foreach ($areas as $area): ?>
        <option value="<?= (int) $area['id'] ?>" <?= (int) $area['id'] === (int) $empleado['area_id'] ? 'selected' : '' ?>><?= e($area['nombre']) ?></option>
        <?php endforeach; ?>
    </select>

    <div class="acciones">
        <button type="submit">Guardar</button>
        <a class="boton secundario" href="/empleados/listar.php">Cancelar</a>
    </div>
</form>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
