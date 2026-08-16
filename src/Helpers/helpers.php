<?php

declare(strict_types=1);

function e(?string $valor): string
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $ruta): never
{
    header('Location: ' . $ruta);
    exit;
}

function usuario_actual(): ?array
{
    if (!isset($_SESSION['usuario_id'])) {
        return null;
    }

    return [
        'id' => $_SESSION['usuario_id'],
        'nombre_usuario' => $_SESSION['nombre_usuario'],
        'rol' => $_SESSION['rol'],
    ];
}

/**
 * @param string[] $rolesPermitidos
 */
function require_role(array $rolesPermitidos): void
{
    $usuario = usuario_actual();

    if ($usuario === null) {
        redirect('/login.php');
    }

    if (!in_array($usuario['rol'], $rolesPermitidos, true)) {
        http_response_code(403);
        echo '<h1>403 - Acceso denegado</h1><p>No tienes permiso para acceder a esta página.</p>';
        exit;
    }
}

function flash_set(string $tipo, string $mensaje): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function flash_get(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

/**
 * Lee y normaliza los campos del formulario de equipo desde $_POST.
 * @return array<string, mixed>
 */
function leer_datos_equipo_post(): array
{
    return [
        'tipo' => $_POST['tipo'] ?? '',
        'marca' => trim($_POST['marca'] ?? ''),
        'modelo' => trim($_POST['modelo'] ?? ''),
        'numero_serie' => trim($_POST['numero_serie'] ?? ''),
        'anio_adquisicion' => (int) ($_POST['anio_adquisicion'] ?? 0),
        'estado' => $_POST['estado'] ?? 'activo',
        'garantia_proveedor' => trim($_POST['garantia_proveedor'] ?? '') ?: null,
        'garantia_inicio' => trim($_POST['garantia_inicio'] ?? '') ?: null,
        'garantia_fin' => trim($_POST['garantia_fin'] ?? '') ?: null,
        'area_id' => trim($_POST['area_id'] ?? '') !== '' ? (int) $_POST['area_id'] : null,
    ];
}

function validar_datos_equipo(array $datos): ?string
{
    if (!in_array($datos['tipo'], ['laptop', 'escritorio'], true)) {
        return 'El tipo de equipo no es válido.';
    }
    if ($datos['marca'] === '' || $datos['modelo'] === '' || $datos['numero_serie'] === '') {
        return 'Marca, modelo y número de serie son obligatorios.';
    }
    if ($datos['anio_adquisicion'] < 1990 || $datos['anio_adquisicion'] > 2100) {
        return 'El año de adquisición no es válido.';
    }

    return null;
}

function badge_estado_equipo(string $estado): string
{
    $mapa = [
        'activo' => ['badge-success', 'Activo'],
        'en_reparacion' => ['badge-warning', 'En reparación'],
        'de_baja' => ['badge-danger', 'De baja'],
    ];
    [$clase, $texto] = $mapa[$estado] ?? ['badge-consulta', $estado];

    return '<span class="' . $clase . '">' . e($texto) . '</span>';
}

function badge_rol(string $rol): string
{
    $clase = $rol === 'admin' ? 'badge-admin' : 'badge-consulta';

    return '<span class="' . $clase . '">' . e(ucfirst($rol)) . '</span>';
}
