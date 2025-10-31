<?php
?>
<section class="seccion-carrusel">
    <h2 class="titulo-carrusel">
        <i class="fas fa-tag icono-titulo"></i> <?php echo $titulo; ?>
    </h2>
    <div class="controles-carrusel">
        <button class="prev-btn" data-carrusel-id="<?php echo $id_carrusel; ?>">&lt;</button>
        <button class="next-btn" data-carrusel-id="<?php echo $id_carrusel; ?>">&gt;</button>
    </div>
    
    <div class="carrusel-productos" id="<?php echo $id_carrusel; ?>">
        <?php foreach ($productos as $producto): ?>
        <div class="tarjeta-producto">
            <img src="<?php echo htmlspecialchars($producto['imagen_ruta']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
            <h4 class="nombre-producto"><?php echo $producto['nombre']; ?></h4>
            <p class="precio-producto"><?php echo number_format($producto['precio'], 2, ',', '.') . ' €'; ?></p>
            
            <?php if (isset($puede_editar) && $puede_editar): ?>
            <div class="acciones-empleado">
                <a href="index.php?ruta=editar_producto&id=<?php echo $producto['id']; ?>" class="boton-secundario boton-chico">✏️ Editar</a>
                
                <form action="index.php?ruta=eliminar_producto" method="POST" style="display:inline;" onsubmit="return confirm('¿Confirmas que deseas eliminar el producto: <?php echo htmlspecialchars($producto['nombre']); ?>?');">
                    <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                    <button type="submit" class="boton-eliminar boton-chico">🗑️ Eliminar</button>
                </form>
            </div>
            <?php else: ?>
            <button class="comprar-btn">Comprar</button>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</section>