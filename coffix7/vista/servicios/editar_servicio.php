<?php
if (!isset($servicio) || !isset($categorias_agrupadas)) {
    echo '<section class="contenedor-formulario-servicio"><h2>Error: Servicio no encontrado o sin permisos / Error al cargar categorías.</h2></section>';
    return;
}

$imagen_actual = $servicio['imagenes'] ?? '';
$categoria_actual = $servicio['categoria'] ?? '';
?>

<section class="contenedor-formulario-servicio">
    <h2 class="titulo-formulario">Editar Servicio: <?php echo htmlspecialchars($servicio['titulo']); ?></h2>
    <?php if (isset($_SESSION['mensaje_error'])): ?>
        <p class="mensaje-error"><?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?></p>
    <?php endif; ?>

    <form action="index.php?ruta=procesar_edicion_servicio" method="POST" enctype="multipart/form-data" class="formulario-estilo-medieval">
        <input type="hidden" name="id_servicio" value="<?php echo $servicio['id']; ?>">

        <div class="grupo-input">
            <label for="titulo">Titulo del Servicio (Obligatorio):</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($servicio['titulo']); ?>" required>
        </div>

        <div class="grupo-input">
            <label for="descripcion">Descripcion Detallada (Obligatorio):</label>
            <textarea id="descripcion" name="descripcion" rows="6" required><?php echo htmlspecialchars($servicio['descripcion']); ?></textarea>
        </div>

        <div class="grupo-input">
            <label for="categoria">Categoria (Obligatorio):</label>
            <select id="categoria" name="categoria" required>
                <option value="" disabled>Seleccione una subcategoría...</option>
                
                <?php
                foreach ($categorias_agrupadas as $principal => $subcategorias): 
                ?>
                    <optgroup label="<?php echo htmlspecialchars($principal); ?>">
                    <?php
                    foreach ($subcategorias as $subcat):
                        $selected = ($categoria_actual === $subcat) ? 'selected' : '';
                    ?>
                        <option value="<?php echo htmlspecialchars($subcat); ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($subcat); ?>
                        </option>
                    <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grupo-input">
            <label for="ubicacion">Ubicacion (Ciudad/Zona - Obligatorio):</label>
            <input type="text" id="ubicacion" name="ubicacion" value="<?php echo htmlspecialchars($servicio['ubicacion']); ?>" required>
        </div>

        <div class="grupo-input">
            <label for="precio">Precio ($ - Obligatorio):</label>
            <input type="number" step="0.01" id="precio" name="precio" value="<?php echo htmlspecialchars($servicio['precio']); ?>" required min="0">
        </div>

        <div class="grupo-input">
            <label for="disponibilidad">Disponibilidad (Horarios/Dias):</label>
            <input type="text" id="disponibilidad" name="disponibilidad" value="<?php echo htmlspecialchars($servicio['disponibilidad']); ?>">
        </div>

        <div class="grupo-input">
            <label>Imagen Actual:</label>
            <?php if (!empty($imagen_actual)): ?>
                <div class="miniaturas-actuales" style="margin-bottom: 10px;">
                    <img src="<?php echo htmlspecialchars($imagen_actual); ?>" style="width: 50px; height: 50px; object-fit: cover; border: 1px solid #ccc;">
                </div>
            <?php else: ?>
                <p>No hay imagen actual.</p>
            <?php endif; ?>
        </div>

        <div class="grupo-input">
            <label for="imagen_servicio">Cargar Nueva Imagen (Reemplazara la anterior):</label>
            <input type="file" id="imagen_servicio" name="imagenes[]" accept="image/*">
        </div>

        <button type="submit">Guardar Cambios</button>
    </form>
</section>