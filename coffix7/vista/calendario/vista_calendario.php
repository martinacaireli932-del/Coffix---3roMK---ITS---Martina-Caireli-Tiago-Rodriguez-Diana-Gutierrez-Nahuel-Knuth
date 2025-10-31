<?php 
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php?ruta=iniciar_sesion');
    exit();
}

$id_usuario = $_SESSION['usuario']['id'];
$rol = $_SESSION['usuario']['rol']; 
?>

<div class="contenedor-calendario">
    <h1>📅 Mi Calendario de Reservaciones <?php echo date('Y'); ?></h1>
    
    <?php if ($rol === 'Cliente'): ?>
        <p>Aquí ves las reservas que has solicitado y han sido <span style="font-weight: bold; color: green;">Aceptadas</span> por el proveedor.</p>
    <?php elseif ($rol === 'Proveedor' || $rol === 'Psicologo'): ?>
        <p>Este calendario muestra todas las reservas que has <span style="font-weight: bold; color: blue;">Aceptado</span>. Las solicitudes pendientes se gestionan en <a href="index.php?ruta=notificaciones">Notificaciones</a>.</p>
    <?php endif; ?>

    <div id='fullCalendar'></div>

</div>

<link rel='stylesheet' href='recursos/css/calendario.css' /> 

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script src='recursos/js/calendario.js'></script>