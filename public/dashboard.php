<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

require_role(['admin', 'consulta']);

$titulo = 'Inicio';
require __DIR__ . '/partials/layout_inicio.php';
?>
<h1>Panel de control</h1>
<div class="tarjetas">
    <div class="tarjeta"><a href="/equipos/listar.php">Equipos</a></div>
    <div class="tarjeta"><a href="/empleados/listar.php">Empleados</a></div>
    <div class="tarjeta"><a href="/areas/listar.php">Áreas</a></div>
    <div class="tarjeta"><a href="/asignaciones/asignar.php">Asignar equipo</a></div>
    <div class="tarjeta"><a href="/reportes/por_area.php">Reporte por área</a></div>
    <div class="tarjeta"><a href="/reportes/garantias_activas.php">Garantías activas</a></div>
    <div class="tarjeta"><a href="/reportes/por_anio.php">Reporte por año</a></div>
</div>
<?php require __DIR__ . '/partials/layout_fin.php'; ?>
