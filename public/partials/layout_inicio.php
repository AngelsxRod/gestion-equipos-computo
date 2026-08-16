<?php
/**
 * Incluir después de definir $titulo. Requiere que bootstrap.php ya se haya cargado.
 */
$usuario = usuario_actual();
$flash = flash_get();
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title><?= e($titulo ?? 'Control de Cómputo') ?></title>
<link rel="stylesheet" href="/assets/css/estilo.css">
</head>
<body>
<div class="barra">
    <div>
        <span class="marca">Control de Cómputo</span>
        <?php if ($usuario): ?>
            <a href="/dashboard.php">Inicio</a>
            <a href="/equipos/listar.php">Equipos</a>
            <a href="/empleados/listar.php">Empleados</a>
            <a href="/areas/listar.php">Áreas</a>
            <a href="/reportes/por_area.php">Reportes</a>
        <?php endif; ?>
    </div>
    <?php if ($usuario): ?>
        <div>
            <?= e($usuario['nombre_usuario']) ?> (<?= e($usuario['rol']) ?>)
            &nbsp;<a href="/logout.php">Salir</a>
        </div>
    <?php endif; ?>
</div>
<main>
<?php if ($flash): ?>
    <div class="flash <?= e($flash['tipo']) ?>"><?= e($flash['mensaje']) ?></div>
<?php endif; ?>
