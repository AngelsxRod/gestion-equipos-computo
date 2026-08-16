<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Auth\Csrf;
use App\Models\Area;

require_role(['admin']);

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$area = Area::buscarPorId($id);

if ($area === null) {
    flash_set('error', 'Área no encontrada.');
    redirect('/areas/listar.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validar($_POST['csrf_token'] ?? null)) {
        flash_set('error', 'Sesión expirada, intenta de nuevo.');
        redirect('/areas/listar.php');
    }

    if (Area::eliminar($id)) {
        flash_set('exito', 'Área eliminada correctamente.');
    } else {
        flash_set('error', 'No se puede eliminar: el área tiene empleados activos.');
    }
    redirect('/areas/listar.php');
}

$titulo = 'Eliminar área';
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1>Eliminar área</h1>
<p>¿Confirmas eliminar el área "<strong><?= e($area['nombre']) ?></strong>"?</p>
<form method="post" class="card max-w-lg">
    <?= Csrf::campoHtml() ?>
    <input type="hidden" name="id" value="<?= (int) $area['id'] ?>">
    <div class="mt-6 flex gap-2">
        <button type="submit" class="btn-danger">Sí, eliminar</button>
        <a class="btn-secondary" href="/areas/listar.php">Cancelar</a>
    </div>
</form>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
