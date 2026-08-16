<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Auth\Csrf;
use App\Models\Asignacion;
use App\Models\Empleado;
use App\Models\Equipo;

require_role(['admin']);

$equipos = Equipo::listarParaAsignar();
$empleados = Empleado::listarActivos();
$equipoIdPreseleccionado = (int) ($_GET['equipo_id'] ?? 0);
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validar($_POST['csrf_token'] ?? null)) {
        $error = 'Sesión expirada, intenta de nuevo.';
    } else {
        $equipoId = (int) ($_POST['equipo_id'] ?? 0);
        $empleadoId = (int) ($_POST['empleado_id'] ?? 0);
        $fechaAsignacion = trim($_POST['fecha_asignacion'] ?? '');
        $observaciones = trim($_POST['observaciones'] ?? '') ?: null;

        if ($equipoId === 0 || $empleadoId === 0 || $fechaAsignacion === '') {
            $error = 'Equipo, empleado y fecha son obligatorios.';
        } else {
            Asignacion::asignarEquipo(
                $equipoId,
                $empleadoId,
                $fechaAsignacion,
                usuario_actual()['id'],
                $observaciones
            );
            flash_set('exito', 'Equipo asignado correctamente.');
            redirect('/equipos/ver.php?id=' . $equipoId);
        }
    }
}

$titulo = 'Asignar equipo';
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1>Asignar equipo</h1>
<?php if ($error): ?><div class="flash-error"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="card max-w-lg">
    <?= Csrf::campoHtml() ?>

    <label for="equipo_id">Equipo</label>
    <select id="equipo_id" name="equipo_id" required>
        <option value="">-- Selecciona --</option>
        <?php foreach ($equipos as $equipo): ?>
        <option value="<?= (int) $equipo['id'] ?>" <?= $equipoIdPreseleccionado === (int) $equipo['id'] ? 'selected' : '' ?>>
            <?= e($equipo['marca']) ?> <?= e($equipo['modelo']) ?> (<?= e($equipo['numero_serie']) ?>)
        </option>
        <?php endforeach; ?>
    </select>

    <label for="empleado_id">Empleado</label>
    <select id="empleado_id" name="empleado_id" required>
        <option value="">-- Selecciona --</option>
        <?php foreach ($empleados as $empleado): ?>
        <option value="<?= (int) $empleado['id'] ?>"><?= e($empleado['nombre_completo']) ?> (<?= e($empleado['area_nombre']) ?>)</option>
        <?php endforeach; ?>
    </select>

    <label for="fecha_asignacion">Fecha de asignación</label>
    <input type="date" id="fecha_asignacion" name="fecha_asignacion" value="<?= e(date('Y-m-d')) ?>" required>

    <label for="observaciones">Observaciones (opcional)</label>
    <input type="text" id="observaciones" name="observaciones">

    <p><em>Si el equipo ya tenía una asignación activa, se cerrará automáticamente con esta misma fecha.</em></p>

    <div class="mt-6 flex gap-2">
        <button type="submit" class="btn-primary">Asignar</button>
        <a class="btn-secondary" href="/equipos/listar.php">Cancelar</a>
    </div>
</form>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
