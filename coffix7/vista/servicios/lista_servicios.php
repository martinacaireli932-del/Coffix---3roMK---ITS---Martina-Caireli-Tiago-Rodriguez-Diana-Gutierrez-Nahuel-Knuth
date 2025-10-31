<?php
?>
<section class="contenedor-servicios">
    <h2 class="titulo-servicios">🌟 Servicios de la Comunidad</h2>

    <?php if (isset($_SESSION['mensaje_exito'])): ?>
        <p class="mensaje-exito"><?php echo $_SESSION['mensaje_exito']; unset($_SESSION['mensaje_exito']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['mensaje_error'])): ?>
        <p class="mensaje-error"><?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?></p>
    <?php endif; ?>
    
    <form action="index.php" method="GET" class="filtro-servicios">
        <input type="hidden" name="ruta" value="servicios">
        
        <input type="text" name="busqueda" placeholder="Palabra clave o Categoría" value="<?php echo htmlspecialchars($_GET['busqueda'] ?? ''); ?>">
        <input type="text" name="ubicacion" placeholder="Ubicación (Ej: Santiago)" value="<?php echo htmlspecialchars($_GET['ubicacion'] ?? ''); ?>">
        
        <select name="precio_max">
            <option value="">Precio Máximo</option>
            <?php $precio_max_selected = $_GET['precio_max'] ?? ''; ?>
            <option value="50" <?php echo ($precio_max_selected == '50') ? 'selected' : ''; ?>>Hasta $50</option>
            <option value="100" <?php echo ($precio_max_selected == '100') ? 'selected' : ''; ?>>Hasta $100</option>
        </select>
        
        <select name="calificacion_min">
            <option value="">Calificación Mínima</option>
            <?php $calif_min_selected = $_GET['calificacion_min'] ?? ''; ?>
            <option value="4.5" <?php echo ($calif_min_selected == '4.5') ? 'selected' : ''; ?>>4.5+</option>
            <option value="4.0" <?php echo ($calif_min_selected == '4.0') ? 'selected' : ''; ?>>4.0+</option>
        </select>
        
        <button type="submit">🔍 Buscar y Filtrar</button>
    </form>

    <div class="lista-servicios">
        
        <?php if (empty($servicios)): ?>
            <p class="no-encontrado">No se encontraron servicios que coincidan con tu búsqueda.</p>
        <?php endif; ?>

        <?php foreach ($servicios as $servicio): 
            $ruta_imagen_principal = $servicio['imagenes'] ?? 'recursos/img/servicios/default.png';
            if (empty($ruta_imagen_principal)) {
                $ruta_imagen_principal = 'recursos/img/servicios/default.png';
            }
            
            $calificacion = $servicio['calificacion'] ?? 0;
            $foto_perfil = $servicio['foto_perfil'] ?? 'recursos/img/usuarios/default.png';
            $nombre_usuario = $servicio['nombre_usuario'] ?? 'Anónimo';
            $precio = $servicio['precio'] ?? 0;
        ?>
        <div class="tarjeta-servicio">
            <div class="servicio-media">
                <img src="<?php echo htmlspecialchars($ruta_imagen_principal); ?>" alt="Imagen de servicio">
            </div>
            <div class="servicio-info">
                <h3><a href="index.php?ruta=detalle_servicio&id=<?php echo $servicio['id']; ?>"><?php echo htmlspecialchars($servicio['titulo'] ?? 'Sin título'); ?></a></h3>
                <p class="servicio-descripcion"><?php echo substr(htmlspecialchars($servicio['descripcion'] ?? 'Sin descripción', ENT_QUOTES), 0, 100); ?>...</p>
                
                <div class="servicio-datos">
                    <span>📍 <?php echo htmlspecialchars($servicio['ubicacion'] ?? 'N/A'); ?></span>
                    <span>🏷️ <?php echo htmlspecialchars($servicio['categoria'] ?? 'N/A'); ?></span>
                    <span>⭐ <?php echo number_format($calificacion, 1); ?></span> 
                </div>
                
                <p class="servicio-precio">Precio: <strong>$<?php echo number_format($precio, 2); ?></strong></p>
                
                <div class="servicio-meta">
                    <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto de Perfil" class="foto-publicador">
                    <span class="nombre-publicador">Por: <?php echo htmlspecialchars($nombre_usuario); ?></span> 
                    
                    <span class="fecha-publicacion">Publicado el: <?php echo date('d/m/Y', strtotime($servicio['fecha_publicacion'] ?? 'now')); ?></span>
                </div>
                
                <a href="index.php?ruta=detalle_servicio&id=<?php echo $servicio['id']; ?>" class="boton-detalle">Ver Detalles</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>