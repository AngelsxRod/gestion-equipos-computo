<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Auth\Csrf;
use App\Models\Area;
use App\Models\Equipo;

require_role(['admin']);

$areas = Area::listarActivas();
$equipo = [];
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validar($_POST['csrf_token'] ?? null)) {
        $error = 'Sesión expirada, intenta de nuevo.';
    } else {
        $datos = leer_datos_equipo_post();
        $error = validar_datos_equipo($datos);

        if ($error === null) {
            try {
                Equipo::crear($datos);
                flash_set('exito', 'Equipo creado correctamente.');
                redirect('/equipos/listar.php');
            } catch (\PDOException $e) {
                $error = 'Ya existe un equipo con ese número de serie.';
            }
        }
        $equipo = $datos;
    }
}

$accionUrl = '/equipos/crear.php';
$titulo = 'Nuevo equipo';
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1>Nuevo equipo</h1>
<?php require __DIR__ . '/_formulario.php'; ?>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
