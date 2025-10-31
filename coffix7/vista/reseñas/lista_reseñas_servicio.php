<div class="listado-reseñas">
    
    <?php foreach ($reseñas as $reseña): ?>
        <div class="reseña-item">
            <div class="reseña-meta">
                <span class="nombre-usuario-reseña">
                    <?php
                    $nombre_completo = trim(($reseña['nombre'] ?? 'Usuario') . ' ' . ($reseña['apellido'] ?? 'Anónimo'));
                    echo htmlspecialchars($nombre_completo); 
                    ?>
                </span>
                
                <span class="calificacion-reseña">
                    <?php 
                    $estrellas = str_repeat('⭐', $reseña['calificacion'] ?? 0);
                    echo htmlspecialchars($estrellas);
                    ?>
                </span>
                
                <span class="fecha-reseña">
                    Fecha: <?php echo date('d/m/Y', strtotime($reseña['fecha_creacion'] ?? 'now')); ?>
                </span>
            </div>
            
            <p class="reseña-comentario">
                <?php echo nl2br(htmlspecialchars($reseña['comentario'] ?? 'Sin comentario.')); ?>
            </p>
        </div>
    <?php endforeach; ?>
    
</div>