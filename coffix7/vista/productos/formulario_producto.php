<?php
$es_edicion = ($accion === 'editar' && $producto !== null);
$titulo_pagina = $es_edicion ? '✏️ Editar Producto: ' . htmlspecialchars($producto['nombre'] ?? 'Desconocido') : '➕ Agregar Nuevo Producto';
$url_destino = $es_edicion ? 'index.php?ruta=procesar_edicion' : 'index.php?ruta=procesar_adicion';
$boton_texto = $es_edicion ? 'Guardar Cambios' : 'Crear Producto';
$nombre = $es_edicion ? htmlspecialchars($producto['nombre'] ?? '') : '';
$descripcion = $es_edicion ? htmlspecialchars($producto['descripcion'] ?? '') : '';
$precio = $es_edicion ? htmlspecialchars($producto['precio'] ?? '') : '';
$categoria = $es_edicion ? htmlspecialchars($producto['categoria'] ?? '') : '';
$stock = $es_edicion ? htmlspecialchars($producto['stock'] ?? '') : '';
$imagen_actual = $es_edicion ? htmlspecialchars($producto['imagen'] ?? '') : '';
$destacado_checked = $es_edicion && ($producto['destacado'] ?? 0) == 1 ? 'checked' : '';
$categorias_disponibles = ['Bebidas', 'Comidas', 'Postres', 'Artesanal'];
?>

<div class="contenedor-formulario-producto">
    <h1><?php echo $titulo_pagina; ?></h1>
    
    <form action="<?php echo $url_destino; ?>" method="POST" enctype="multipart/form-data" class="formulario-gestion">
        
        <?php if ($es_edicion): ?>
            <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($producto['id']); ?>">
        <?php endif; ?>

        <div class="grupo-campo">
            <label for="nombre">Nombre del Producto:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>
        </div>

        <div class="grupo-campo">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="4" required><?php echo $descripcion; ?></textarea>
        </div>

        <div class="grupo-campo campo-doble">
            <div class="sub-campo">
                <label for="precio">Precio (€):</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0" value="<?php echo $precio; ?>" required>
            </div>
            <div class="sub-campo">
                <label for="stock">Stock Disponible:</label>
                <input type="number" id="stock" name="stock" min="0" value="<?php echo $stock; ?>" required>
            </div>
        </div>
        
        <div class="grupo-campo">
            <label for="categoria">Categoría:</label>
            <select id="categoria" name="categoria" required>
                <option value="">-- Seleccione una categoría --</option>
                <?php foreach ($categorias_disponibles as $cat): ?>
                    <option value="<?php echo $cat; ?>" <?php echo ($cat === $categoria) ? 'selected' : ''; ?>>
                        <?php echo $cat; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grupo-campo">
            <label for="imagen">Imagen del Producto:</label>
            <?php if ($es_edicion && !empty($imagen_actual)): ?>
                <p>Imagen actual:</p>
                <img src="<?php echo $imagen_actual; ?>" alt="Imagen actual" class="imagen-actual-form">
                <p class="nota-imagen">Deje vacío si no desea modificar la imagen actual.</p>
            <?php endif; ?>
            <input type="file" id="imagen" name="imagen" accept="image/*" <?php echo $es_edicion && !empty($imagen_actual) ? '' : 'required'; ?>>
        </div>

        <?php if ($es_edicion): ?>
        <div class="grupo-campo">
            <input type="checkbox" id="destacado" name="destacado" value="1" <?php echo $destacado_checked; ?>>
            <label for="destacado" style="display:inline-block; margin-left: 10px;">Marcar como Producto Destacado</label>
        </div>
        <?php endif; ?>

        <div class="grupo-campo grupo-botones">
            <button type="submit" class="boton-primario"><?php echo $boton_texto; ?></button>
            <a href="index.php?ruta=inicio" class="boton-secundario">Cancelar</a>
        </div>
        
    </form>
</div>