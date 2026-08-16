<?php
/**
 * Incluir después de definir $titulo. Requiere que bootstrap.php ya se haya cargado.
 */
$usuario = usuario_actual();
$flash = flash_get();
$seccionActual = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'))[0] ?? '';

function nav_clase(string $seccion, string $actual): string
{
    return $seccion === $actual ? 'nav-link-active' : 'nav-link';
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($titulo ?? 'Control de Cómputo') ?></title>
<link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen">
<?php if ($usuario): ?>
<nav class="bg-slate-900 text-white px-4 sm:px-6 py-3 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-6 flex-wrap">
        <span class="font-bold text-white">Control de Cómputo</span>
        <a class="<?= basename($_SERVER['SCRIPT_NAME']) === 'dashboard.php' ? 'nav-link-active' : 'nav-link' ?>" href="/dashboard.php">Inicio</a>
        <a class="<?= nav_clase('equipos', $seccionActual) ?>" href="/equipos/listar.php">Equipos</a>
        <a class="<?= nav_clase('empleados', $seccionActual) ?>" href="/empleados/listar.php">Empleados</a>
        <a class="<?= nav_clase('areas', $seccionActual) ?>" href="/areas/listar.php">Áreas</a>
        <a class="<?= nav_clase('reportes', $seccionActual) ?>" href="/reportes/por_area.php">Reportes</a>
    </div>
    <div class="flex items-center gap-3 text-sm">
        <span><?= e($usuario['nombre_usuario']) ?></span>
        <?= badge_rol($usuario['rol']) ?>
        <a class="nav-link" href="/logout.php">Salir</a>
    </div>
</nav>
<?php endif; ?>
<main class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
<?php if ($flash): ?>
    <div class="flash-<?= e($flash['tipo']) ?>">
        <span><?= $flash['tipo'] === 'exito' ? '✔' : '⚠' ?></span>
        <span><?= e($flash['mensaje']) ?></span>
    </div>
<?php endif; ?>
