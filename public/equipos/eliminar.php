<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Auth\Csrf;
use App\Models\Equipo;

require_role(['admin']);

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$equipo = Equipo::buscarPorId($id);

if ($equipo === null) {
    flash_set('error', 'Equipo no encontrado.');
    redirect('/equipos/listar.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validar($_POST['csrf_token'] ?? null)) {
        flash_set('error', 'Sesión expirada, intenta de nuevo.');
        redirect('/equipos/listar.php');
    }

    if (Equipo::eliminar($id)) {
        flash_set('exito', 'Equipo eliminado correctamente.');
    } else {
        flash_set('error', 'No se puede eliminar: el equipo tiene una asignación activa. Devuélvelo primero.');
    }
    redirect('/equipos/listar.php');
}

$titulo = 'Eliminar equipo';
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1>Eliminar equipo</h1>
<p>¿Confirmas eliminar el equipo "<strong><?= e($equipo['marca']) ?> <?= e($equipo['modelo']) ?> (<?= e($equipo['numero_serie']) ?>)</strong>"?</p>
<form method="post" class="formulario">
    <?= Csrf::campoHtml() ?>
    <input type="hidden" name="id" value="<?= (int) $equipo['id'] ?>">
    <div class="acciones">
        <button type="submit" class="peligro">Sí, eliminar</button>
        <a class="boton secundario" href="/equipos/listar.php">Cancelar</a>
    </div>
</form>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
