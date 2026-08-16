<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Auth\Csrf;
use App\Models\Area;

require_role(['admin']);

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
                Area::crear($nombre);
                flash_set('exito', 'Área creada correctamente.');
                redirect('/areas/listar.php');
            } catch (\PDOException $e) {
                $error = 'Ya existe un área con ese nombre.';
            }
        }
    }
}

$titulo = 'Nueva área';
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1>Nueva área</h1>
<?php if ($error): ?><div class="flash-error"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="card max-w-lg">
    <?= Csrf::campoHtml() ?>
    <label for="nombre">Nombre</label>
    <input type="text" id="nombre" name="nombre" value="<?= e($_POST['nombre'] ?? '') ?>" required autofocus>
    <div class="mt-6 flex gap-2">
        <button type="submit" class="btn-primary">Guardar</button>
        <a class="btn-secondary" href="/areas/listar.php">Cancelar</a>
    </div>
</form>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
