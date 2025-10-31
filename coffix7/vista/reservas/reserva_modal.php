<?php
if (!isset($servicio) || !isset($_SESSION['usuario'])) return;
?>
<div id="reservaModal" class="modal">
    <div class="modal-contenido">
        <span class="cerrar">&times;</span>
        <h3>Solicitar Reserva para: <?php echo htmlspecialchars($servicio['titulo']); ?></h3>
        <p>Publicado por: <strong><?php echo htmlspecialchars($servicio['nombre_usuario'] ?? 'Proveedor'); ?></strong></p>
        
        <form id="formSolicitudReserva">
            <input type="hidden" name="id_servicio" value="<?php echo htmlspecialchars($servicio['id']); ?>">
            <input type="hidden" id="idProveedor" value="<?php echo htmlspecialchars($servicio['id_usuario']); ?>"> <div id="contenedorCalendario" class="contenedor-calendario">
                <h4>Horario de Disponibilidad del Proveedor:</h4>
                <div id="calendarioDisponibilidad">Cargando disponibilidad...</div>
            </div>
            <label for="fecha_reserva">Fecha Deseada:</label>
            <input type="date" id="fecha_reserva" name="fecha" required min="<?php echo date('Y-m-d'); ?>">
            
            <label for="hora_reserva">Hora Específica:</label>
            <input type="time" id="hora_reserva" name="hora" required>
            
            <button type="submit" class="boton-enviar-reserva" style="margin-top: 20px;">Enviar Solicitud de Reserva</button>
        </form>
    </div>
</div>