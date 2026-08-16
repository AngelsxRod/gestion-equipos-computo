<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use App\Reports\Dashboard;

require_role(['admin', 'consulta']);

$kpis = Dashboard::kpis();

$titulo = 'Inicio';
require __DIR__ . '/partials/layout_inicio.php';
?>
<h1>Panel de control</h1>

<div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-8">
    <div class="kpi-card">
        <span class="kpi-value"><?= (int) $kpis['equipos_activos'] ?></span>
        <span class="kpi-label">Equipos activos</span>
    </div>
    <div class="kpi-card">
        <span class="kpi-value"><?= (int) $kpis['equipos_con_garantia_vigente'] ?></span>
        <span class="kpi-label">Con garantía vigente</span>
    </div>
    <div class="kpi-card">
        <span class="kpi-value"><?= (int) $kpis['equipos_sin_asignar'] ?></span>
        <span class="kpi-label">Sin asignar</span>
    </div>
    <div class="kpi-card">
        <span class="kpi-value"><?= (int) $kpis['equipos_en_reparacion'] ?></span>
        <span class="kpi-label">En reparación</span>
    </div>
    <div class="kpi-card">
        <span class="kpi-value"><?= (int) $kpis['total_empleados'] ?></span>
        <span class="kpi-label">Empleados</span>
    </div>
    <div class="kpi-card">
        <span class="kpi-value"><?= (int) $kpis['total_areas'] ?></span>
        <span class="kpi-label">Áreas</span>
    </div>
</div>

<h2>Accesos rápidos</h2>
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
    <a class="card hover:shadow-md transition-shadow" href="/equipos/listar.php">Equipos</a>
    <a class="card hover:shadow-md transition-shadow" href="/empleados/listar.php">Empleados</a>
    <a class="card hover:shadow-md transition-shadow" href="/areas/listar.php">Áreas</a>
    <a class="card hover:shadow-md transition-shadow" href="/asignaciones/asignar.php">Asignar equipo</a>
    <a class="card hover:shadow-md transition-shadow" href="/reportes/por_area.php">Reporte por área</a>
    <a class="card hover:shadow-md transition-shadow" href="/reportes/garantias_activas.php">Garantías activas</a>
    <a class="card hover:shadow-md transition-shadow" href="/reportes/por_anio.php">Reporte por año</a>
</div>
<?php require __DIR__ . '/partials/layout_fin.php'; ?>
