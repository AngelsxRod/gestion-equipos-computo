<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Auth\Csrf;
use App\Models\Empleado;

require_role(['admin']);

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$empleado = Empleado::buscarPorId($id);

if ($empleado === null) {
    flash_set('error', 'Empleado no encontrado.');
    redirect('/empleados/listar.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validar($_POST['csrf_token'] ?? null)) {
        flash_set('error', 'Sesión expirada, intenta de nuevo.');
        redirect('/empleados/listar.php');
    }

    Empleado::eliminar($id);
    flash_set('exito', 'Empleado eliminado correctamente.');
    redirect('/empleados/listar.php');
}

$titulo = 'Eliminar empleado';
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1>Eliminar empleado</h1>
<p>¿Confirmas eliminar a "<strong><?= e($empleado['nombre_completo']) ?></strong>"?</p>
<p><em>Nota: si tiene un equipo asignado actualmente, la asignación quedará en el historial pero el equipo seguirá mostrándose como asignado a esta persona. Devuelve el equipo primero si corresponde.</em></p>
<form method="post" class="card max-w-lg">
    <?= Csrf::campoHtml() ?>
    <input type="hidden" name="id" value="<?= (int) $empleado['id'] ?>">
    <div class="mt-6 flex gap-2">
        <button type="submit" class="btn-danger">Sí, eliminar</button>
        <a class="btn-secondary" href="/empleados/listar.php">Cancelar</a>
    </div>
</form>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
