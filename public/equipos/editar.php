<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Auth\Csrf;
use App\Models\Area;
use App\Models\Equipo;

require_role(['admin']);

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$equipo = Equipo::buscarPorId($id);

if ($equipo === null) {
    flash_set('error', 'Equipo no encontrado.');
    redirect('/equipos/listar.php');
}

$areas = Area::listarActivas();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validar($_POST['csrf_token'] ?? null)) {
        $error = 'Sesión expirada, intenta de nuevo.';
    } else {
        $datos = leer_datos_equipo_post();
        $error = validar_datos_equipo($datos);

        if ($error === null) {
            try {
                Equipo::actualizar($id, $datos);
                flash_set('exito', 'Equipo actualizado correctamente.');
                redirect('/equipos/listar.php');
            } catch (\PDOException $e) {
                $error = 'Ya existe un equipo con ese número de serie.';
            }
        }
        $equipo = $datos + ['id' => $id];
    }
}

$accionUrl = '/equipos/editar.php?id=' . $id;
$titulo = 'Editar equipo';
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1>Editar equipo</h1>
<?php require __DIR__ . '/_formulario.php'; ?>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
