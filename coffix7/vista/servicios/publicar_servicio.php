<?php
if (!isset($categorias_agrupadas)) {
    $categorias_agrupadas = ['Error de Carga' => ['Recargar página']]; 
}
?>
<section class="contenedor-formulario-servicio">
    <h2 class="titulo-formulario">✍️ Publicar un Nuevo Servicio</h2>
    
    <?php if (isset($_SESSION['mensaje_error'])): ?>
        <p class="mensaje-error"><?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?></p>
    <?php endif; ?>

    <form action="index.php?ruta=procesar_publicacion" method="POST" enctype="multipart/form-data" class="formulario-estilo-medieval">
        
        <div class="grupo-input">
            <label for="titulo">Título del Servicio (Obligatorio):</label>
            <input type="text" id="titulo" name="titulo" required>
        </div>
        
        <div class="grupo-input">
            <label for="descripcion">Descripción Detallada (Obligatorio):</label>
            <textarea id="descripcion" name="descripcion" rows="6" required></textarea>
        </div>
        
        <div class="grupo-input">
            <label for="categoria">Categoría (Obligatorio):</label>
            <select id="categoria" name="categoria" required>
                <option value="" disabled selected>Seleccione una subcategoría...</option>
                
                <?php
                foreach ($categorias_agrupadas as $principal => $subcategorias): 
                ?>
                    <optgroup label="<?php echo htmlspecialchars($principal); ?>">
                    <?php 
                    foreach ($subcategorias as $subcat): 
                    ?>
                        <option value="<?php echo htmlspecialchars($subcat); ?>"><?php echo htmlspecialchars($subcat); ?></option>
                    <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="grupo-input">
            <label for="ubicacion">Ubicación (Ciudad/Zona - Obligatorio):</label>
            <input type="text" id="ubicacion" name="ubicacion" placeholder="Ej: Santiago Centro, RM" required>
        </div>

        <div class="grupo-input">
            <label for="precio">Precio ($ - Obligatorio):</label>
            <input type="number" step="0.01" id="precio" name="precio" required min="0">
        </div>
        
        <div class="grupo-input">
            <label for="disponibilidad">Disponibilidad (Horarios/Días):</label>
            <input type="text" id="disponibilidad" name="disponibilidad" placeholder="Ej: Lunes a Viernes, 9:00 - 18:00">
        </div>
        
        <div class="grupo-input">
            <label for="imagen_servicio">Imágenes (Solo se usará la primera):</label> 
            <input type="file" id="imagen_servicio" name="imagenes[]" accept="image/*" required>
        </div>

        <button type="submit">Publicar Servicio</button>
    </form>
</section>