<?php
$esta_logueado = isset($_SESSION['usuario']);
?>

<section class="contenedor-comentarios-web">
    <h2 class="titulo-comentarios">Opiniones sobre COFFIX</h2>
    <p>Ayúdanos a mejorar. Deja tu calificación y reseña sobre el sitio web y tu experiencia general.</p>
    
    <?php if (isset($_SESSION['mensaje_exito'])): ?>
        <p class="mensaje-exito"><?php echo $_SESSION['mensaje_exito']; unset($_SESSION['mensaje_exito']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['mensaje_error'])): ?>
        <p class="mensaje-error"><?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?></p>
    <?php endif; ?>

    <?php if ($esta_logueado): ?>
        <div class="contenedor-formulario-reseña">
            <h4>¡Califica nuestra plataforma!</h4>
            <form action="index.php?ruta=procesar_reseña_web" method="POST" class="formulario-reseña">
                <div class="grupo-input-calificacion">
                    <label>Puntuación (1-5 estrellas):</label>
                    <div class="estrellas">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" id="web_star<?php echo $i; ?>" name="calificacion" value="<?php echo $i; ?>" required>
                            <label for="web_star<?php echo $i; ?>">★</label>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="grupo-input">
                    <label for="comentario_web">Tu Opinión:</label>
                    <textarea id="comentario_web" name="comentario" rows="4" placeholder="¿Qué te pareció el sitio web?" required></textarea>
                </div>
                
                <button type="submit" class="boton-enviar-reseña">Enviar Comentario</button>
            </form>
        </div>
        <hr>
    <?php else: ?>
        <p class="alerta">Debes <a href="index.php?ruta=login">iniciar sesión</a> para dejar un comentario sobre el sitio.</p>
        <hr>
    <?php endif; ?>

    <h3>Comentarios Recientes (<?php echo count($reseñas); ?>)</h3>
    <div class="lista-comentarios">
        <?php if (empty($reseñas)): ?>
            <p>Aún no hay comentarios generales. ¡Sé el primero!</p>
        <?php else: ?>
            <?php foreach ($reseñas as $reseña): ?>
                <div class="comentario-item">
                    <div class="comentario-meta">
                        <img src="<?php echo htmlspecialchars($reseña['foto_perfil']); ?>" alt="Foto de Perfil" class="foto-perfil-reseña">
                        <span class="nombre-usuario-reseña"><strong><?php echo htmlspecialchars($reseña['nombre_usuario']); ?></strong></span>
                        <span class="fecha-reseña"> | <?php echo date('d/m/Y', strtotime($reseña['fecha_reseña'])); ?></span>
                    </div>
                    <div class="comentario-calificacion">
                        <?php 
                        $estrellas = str_repeat('★', $reseña['calificacion']) . str_repeat('☆', 5 - $reseña['calificacion']);
                        echo '<span style="color: gold;">' . $estrellas . '</span>';
                        ?>
                    </div>
                    <p class="comentario-texto"><?php echo nl2br(htmlspecialchars($reseña['comentario'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>