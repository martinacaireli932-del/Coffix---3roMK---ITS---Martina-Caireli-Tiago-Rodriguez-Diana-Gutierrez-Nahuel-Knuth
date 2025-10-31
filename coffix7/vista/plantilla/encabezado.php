<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_usuario_logueado = $_SESSION['usuario']['id'] ?? '';
$rol = $_SESSION['usuario']['rol'] ?? '';

$es_proveedor = in_array($rol, ['Proveedor', 'Psicologo']);
$es_administrador = ($rol === 'Administrador');
$es_empleado = ($rol === 'Empleado');
$es_personal_gestion = ($es_administrador || $es_empleado);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffix - Cafetería Medieval Moderna</title>
    
    <link rel="stylesheet" href="recursos/css/base.css">
    <link rel="stylesheet" href="recursos/css/encabezado_sidebar.css">
    <link rel="stylesheet" href="recursos/css/principal.css">
    <link rel="stylesheet" href="recursos/css/carrusel_productos.css">
    <link rel="stylesheet" href="recursos/css/pie_pagina.css">
    <link rel="stylesheet" href="recursos/css/formularios_perfil.css">
    <link rel="stylesheet" href="recursos/css/servicios.css">
    <link rel="stylesheet" href="recursos/css/reseñas.css">
    <link rel="stylesheet" href="recursos/css/detalle_servicio.css">
    <link rel="stylesheet" href="recursos/css/comunicacion.css">
    
    <link rel="stylesheet" href="recursos/css/gestion_productos.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css">
</head>
<body data-user-id="<?php echo htmlspecialchars($id_usuario_logueado); ?>">

<div class="sidebar" id="sidebar">
    <button class="cerrar-btn" onclick="toggleSidebar()"><i class="fas fa-times"></i></button>
    <h3 class="nombre-pagina">Coffix</h3>
    
    <a href="index.php">Inicio</a>
    <a href="index.php?ruta=menu">Menú</a>
    <a href="index.php?ruta=servicios">Servicios</a>
    <a href="index.php?ruta=acerca">Acerca de</a>
    <a href="index.php?ruta=contacto">Contacto</a>
    <a href="index.php?ruta=comentarios_web">Comentarios</a>

    <hr class="separador-menu">

    <?php
    if (isset($_SESSION['usuario'])):
    ?>
        <a href="index.php?ruta=notificaciones">🔔 Notificaciones</a>
        
        <a href="index.php?ruta=calendario"><i class="fas fa-calendar-alt"></i> Calendario</a>
        
        <?php if ($es_proveedor): ?>
            <a href="index.php?ruta=publicar_servicio">Publicar Servicio</a>
        <?php endif; ?>
        
        <?php if ($es_personal_gestion): ?>
            <a href="index.php?ruta=gestionar_productos">📦 Gestión de Productos</a>
        <?php endif; ?>

        <?php if ($es_administrador): ?>
            <a href="index.php?ruta=servicios_ingresados">Gestión de Página</a>
        <?php endif; ?>

        <a href="index.php?ruta=perfil">Mi Perfil (<?php echo htmlspecialchars($rol); ?>)</a>
        <a href="index.php?ruta=cerrar_sesion">Cerrar Sesión</a>
    <?php else: ?>
        <a href="index.php?ruta=registro">Registrarse</a>
        <a href="index.php?ruta=iniciar_sesion">Iniciar Sesión</a>
    <?php endif; ?>
</div>

<header class="barra-superior">
    <button class="menu-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
    <span class="nombre-pagina">Coffix</span>
    <div class="buscador-container">
        <input type="text" placeholder="Buscar productos..." class="buscador">
        <i class="fas fa-search buscador-icono"></i>
    </div>
    <div class="usuario-logueado">
        <?php if (isset($_SESSION['usuario'])): ?>
            <a href="index.php?ruta=notificaciones" class="notificacion-icon"><i class="fas fa-bell"></i></a>
            
            <i class="fas fa-user-circle"></i>
            
            <span>Hola, <?php echo htmlspecialchars($_SESSION['usuario']['nombre'] ?? 'Usuario'); ?></span>
            
        <?php else: ?>
            <a href="index.php?ruta=iniciar_sesion" class="enlace-login-bar">
                <i class="fas fa-user-circle"></i>
                <span>Iniciar Sesión</span>
            </a>
        <?php endif; ?>
    </div>
</header>

<main class="contenedor-principal">