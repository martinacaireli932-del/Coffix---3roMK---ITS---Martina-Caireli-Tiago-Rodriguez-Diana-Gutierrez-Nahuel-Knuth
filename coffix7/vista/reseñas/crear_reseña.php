<?php
?>

<div class="contenedor-formulario-reseña">
    <h4>Deja tu Calificación y Comentario</h4>
    <form action="index.php?ruta=procesar_reseña_servicio" method="POST" class="formulario-reseña">
        <input type="hidden" name="id_servicio" value="<?php echo htmlspecialchars($servicio['id']); ?>">
        
        <div class="grupo-input-calificacion">
            <label>Puntuación (1-5 estrellas):</label>
            <div class="estrellas">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?php echo $i; ?>" name="calificacion" value="<?php echo $i; ?>" required>
                    <label for="star<?php echo $i; ?>">★</label>
                <?php endfor; ?>
            </div>
        </div>
        
        <div class="grupo-input">
            <label for="comentario">Tu Reseña Escrita:</label>
            <textarea id="comentario" name="comentario" rows="3" placeholder="Escribe tu opinión sobre el servicio..." required></textarea>
        </div>
        
        <button type="submit" class="boton-enviar-reseña">Enviar Reseña</button>
    </form>
</div>