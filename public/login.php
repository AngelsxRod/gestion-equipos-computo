<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use App\Auth\Auth;
use App\Auth\Csrf;

if (usuario_actual() !== null) {
    redirect('/dashboard.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validar($_POST['csrf_token'] ?? null)) {
        $error = 'Sesión expirada, intenta de nuevo.';
    } else {
        $nombreUsuario = trim($_POST['nombre_usuario'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if (Auth::intentarLogin($nombreUsuario, $password)) {
            redirect('/dashboard.php');
        }

        $error = 'Usuario o contraseña incorrectos.';
    }
}

$titulo = 'Iniciar sesión';
require __DIR__ . '/partials/layout_inicio.php';
?>
<div class="max-w-sm mx-auto mt-16 card">
    <h1>Iniciar sesión</h1>
    <?php if ($error): ?>
        <div class="flash-error"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <?= \App\Auth\Csrf::campoHtml() ?>
        <label for="nombre_usuario">Usuario</label>
        <input type="text" id="nombre_usuario" name="nombre_usuario" required autofocus>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

        <div class="mt-6">
            <button type="submit" class="btn-primary w-full justify-center">Entrar</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/partials/layout_fin.php'; ?>
