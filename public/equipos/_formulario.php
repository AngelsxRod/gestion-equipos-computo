<?php
/**
 * Formulario compartido por crear.php y editar.php.
 * Espera: $equipo (array con valores actuales o vacíos), $areas, $error, $accionUrl.
 */
?>
<?php if ($error): ?><div class="flash-error"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="card max-w-2xl" action="<?= e($accionUrl) ?>">
    <?= \App\Auth\Csrf::campoHtml() ?>
    <?php if (!empty($equipo['id'])): ?>
    <input type="hidden" name="id" value="<?= (int) $equipo['id'] ?>">
    <?php endif; ?>

    <label for="tipo">Tipo</label>
    <select id="tipo" name="tipo" required>
        <option value="laptop" <?= ($equipo['tipo'] ?? '') === 'laptop' ? 'selected' : '' ?>>Laptop</option>
        <option value="escritorio" <?= ($equipo['tipo'] ?? '') === 'escritorio' ? 'selected' : '' ?>>Escritorio</option>
    </select>

    <label for="marca">Marca</label>
    <input type="text" id="marca" name="marca" value="<?= e($equipo['marca'] ?? '') ?>" required>

    <label for="modelo">Modelo</label>
    <input type="text" id="modelo" name="modelo" value="<?= e($equipo['modelo'] ?? '') ?>" required>

    <label for="numero_serie">Número de serie</label>
    <input type="text" id="numero_serie" name="numero_serie" value="<?= e($equipo['numero_serie'] ?? '') ?>" required>

    <label for="anio_adquisicion">Año de adquisición</label>
    <input type="number" id="anio_adquisicion" name="anio_adquisicion" min="1990" max="2100"
           value="<?= e((string) ($equipo['anio_adquisicion'] ?? date('Y'))) ?>" required>

    <label for="estado">Estado</label>
    <select id="estado" name="estado" required>
        <option value="activo" <?= ($equipo['estado'] ?? 'activo') === 'activo' ? 'selected' : '' ?>>Activo</option>
        <option value="en_reparacion" <?= ($equipo['estado'] ?? '') === 'en_reparacion' ? 'selected' : '' ?>>En reparación</option>
        <option value="de_baja" <?= ($equipo['estado'] ?? '') === 'de_baja' ? 'selected' : '' ?>>De baja</option>
    </select>

    <label for="area_id">Área (dueña del equipo)</label>
    <select id="area_id" name="area_id">
        <option value="">-- Sin asignar --</option>
        <?php foreach ($areas as $area): ?>
        <option value="<?= (int) $area['id'] ?>" <?= (string) ($equipo['area_id'] ?? '') === (string) $area['id'] ? 'selected' : '' ?>><?= e($area['nombre']) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="garantia_proveedor">Proveedor de garantía (opcional)</label>
    <input type="text" id="garantia_proveedor" name="garantia_proveedor" value="<?= e($equipo['garantia_proveedor'] ?? '') ?>">

    <label for="garantia_inicio">Inicio de garantía (opcional)</label>
    <input type="date" id="garantia_inicio" name="garantia_inicio" value="<?= e($equipo['garantia_inicio'] ?? '') ?>">

    <label for="garantia_fin">Fin de garantía (opcional)</label>
    <input type="date" id="garantia_fin" name="garantia_fin" value="<?= e($equipo['garantia_fin'] ?? '') ?>">

    <div class="mt-6 flex gap-2">
        <button type="submit" class="btn-primary">Guardar</button>
        <a class="btn-secondary" href="/equipos/listar.php">Cancelar</a>
    </div>
</form>
