<?php

if (!isset($productosDestacados)) $productosDestacados = [];
if (!isset($productosMasVendidos)) $productosMasVendidos = [];

$es_empleado = (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'Empleado');
?>

<section class="banner-principal">
    <div class="carrusel-banner" id="carrusel-banner">
        <div class="item-banner activo">
            <img src="recursos/img/productos/banner1.jpg" alt="Café del Bosque">
            <div class="texto-banner">
                <h2>☕ El Café del Bosque</h2>
                <p>Descubre el sabor oculto en nuestra nueva bebida, el Elixir del Dragón.</p>
                <button>Ver Producto</button>
            </div>
        </div>
        <div class="item-banner">
                <img src="recursos/img/productos/banner2.png" alt="Postres Medievales">
            <div class="texto-banner">
                <h2>🍰 Postres de la Corona</h2>
                <p>Nuestra selección de postres con recetas ancestrales.</p>
                <button>Ver Menú</button>
            </div>
        </div>
    </div>
    <div class="controles-banner">
        <button class="prev-banner"><i class="fas fa-chevron-left"></i></button>
        <button class="next-banner"><i class="fas fa-chevron-right"></i></button>
    </div>
</section>

<?php if ($es_empleado): ?>
<div class="contenedor-gestion-productos" style="text-align: center; margin: 20px 0;">
    <a href="index.php?ruta=agregar_producto" class="boton-primario">➕ Agregar Nuevo Producto</a>
</div>
<?php endif; ?>

<section class="menu-categorias">
    <div class="categoria">
        <img src="recursos/img/productos/bebida.jpg" alt="Bebidas">
        <span>Bebidas</span>
    </div>
    <div class="categoria">
        <img src="recursos/img/productos/comida.jpg" alt="Comidas">
        <span>Comidas</span>
    </div>
    <div class="categoria">
        <img src="recursos/img/productos/postre.png" alt="Postres">
        <span>Postres</span>
    </div>
    <div class="categoria">
        <img src="recursos/img/productos/artesanal.png" alt="Artesanal">
        <span>Artesanal</span>
    </div>
</section>

<?php 
$titulo = '👑 Productos Destacados';
$productos = $productosDestacados;
$id_carrusel = 'carrusel-destacados';
$puede_editar = $es_empleado; 
include 'vista/productos/productos_carrusel.php';
?>

<section class="secciones-adicionales">
    <div class="acerca-de">
        <h3>📜 Acerca de Coffix</h3>
        <p>Somos una cafetería que fusiona el encanto medieval con la comodidad moderna. Ven a disfrutar de un ambiente único y sabores legendarios, donde el café es la joya de la corona y cada bocado es una aventura.</p>
        <button>Leer Más</button>
    </div>
    <div class="mapa-ubicacion">
        <h3>📍 Ubicación</h3>
        <div class="mapa-placeholder">
            <iframe 
                src="https://maps.google.com/maps?q=Ellauri%20y%20Solano%20Garc%C3%ADa%2C%20Punta%20Carretas%2C%20Montevideo%2C%20Uruguay&t=&z=16&ie=UTF8&iwloc=&output=embed" 
                width="100%" 
                height="215px" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<?php 
$titulo = '🔥 Los Más Vendidos';
$productos = $productosMasVendidos;
$id_carrusel = 'carrusel-vendidos';
$puede_editar = $es_empleado;
include 'vista/productos/productos_carrusel.php';
?>