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

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validar($_POST['csrf_token'] ?? null)) {
        $error = 'Sesión expirada, intenta de nuevo.';
    } else {
        $nombre = trim($_POST['nombre'] ?? '');
        if ($nombre === '') {
            $error = 'El nombre es obligatorio.';
        } else {
            try {
                Area::actualizar($id, $nombre);
                flash_set('exito', 'Área actualizada correctamente.');
                redirect('/areas/listar.php');
            } catch (\PDOException $e) {
                $error = 'Ya existe un área con ese nombre.';
            }
        }
    }
    $area['nombre'] = $nombre;
}

$titulo = 'Editar área';
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1>Editar área</h1>
<?php if ($error): ?><div class="flash-error"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="card max-w-lg">
    <?= Csrf::campoHtml() ?>
    <input type="hidden" name="id" value="<?= (int) $area['id'] ?>">
    <label for="nombre">Nombre</label>
    <input type="text" id="nombre" name="nombre" value="<?= e($area['nombre']) ?>" required autofocus>
    <div class="mt-6 flex gap-2">
        <button type="submit" class="btn-primary">Guardar</button>
        <a class="btn-secondary" href="/areas/listar.php">Cancelar</a>
    </div>
</form>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
