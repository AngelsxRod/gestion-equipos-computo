<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Auth\Csrf;
use App\Models\Asignacion;

require_role(['admin']);

$asignacionId = (int) ($_GET['asignacion_id'] ?? $_POST['asignacion_id'] ?? 0);
$asignacion = Asignacion::buscarPorId($asignacionId);

if ($asignacion === null || $asignacion['fecha_devolucion'] !== null) {
    flash_set('error', 'Asignación no encontrada o ya devuelta.');
    redirect('/equipos/listar.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validar($_POST['csrf_token'] ?? null)) {
        flash_set('error', 'Sesión expirada, intenta de nuevo.');
        redirect('/equipos/listar.php');
    }

    Asignacion::devolver($asignacionId, date('Y-m-d'));
    flash_set('exito', 'Equipo devuelto correctamente.');
    redirect('/equipos/ver.php?id=' . $asignacion['equipo_id']);
}

$titulo = 'Devolver equipo';
require __DIR__ . '/../partials/layout_inicio.php';
?>
<h1>Devolver equipo</h1>
<p>¿Confirmas registrar la devolución de este equipo con fecha de hoy (<?= e(date('Y-m-d')) ?>)?</p>
<form method="post" class="formulario">
    <?= Csrf::campoHtml() ?>
    <input type="hidden" name="asignacion_id" value="<?= (int) $asignacion['id'] ?>">
    <div class="acciones">
        <button type="submit">Sí, devolver</button>
        <a class="boton secundario" href="/equipos/ver.php?id=<?= (int) $asignacion['equipo_id'] ?>">Cancelar</a>
    </div>
</form>
<?php require __DIR__ . '/../partials/layout_fin.php'; ?>
