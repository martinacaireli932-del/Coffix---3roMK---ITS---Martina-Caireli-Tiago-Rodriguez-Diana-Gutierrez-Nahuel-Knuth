<?php
if (!isset($servicio) || !isset($_SESSION['usuario'])) return;
?>

<div id="solicitudChatModal" class="modal">
    <div class="modal-contenido">
        <span class="cerrar">&times;</span>
        <h3>Enviar Mensaje a <?php echo htmlspecialchars($servicio['nombre_usuario']); ?></h3>
        <p>Tu primer mensaje iniciará una solicitud de chat que debe ser aprobada por el proveedor.</p>
        
        <form id="formSolicitudChat">
            <input type="hidden" name="id_servicio" value="<?php echo htmlspecialchars($servicio['id']); ?>">
            <input type="hidden" name="id_proveedor" value="<?php echo htmlspecialchars($servicio['id_usuario']); ?>">
            
            <label for="primer_mensaje">Tu mensaje inicial:</label>
            <textarea id="primer_mensaje" name="primer_mensaje" rows="4" required placeholder="Escribe tu consulta..."></textarea>
            
            <button type="submit" class="boton-enviar-reseña" style="margin-top: 20px;">Enviar Solicitud</button>
        </form>
    </div>
</div>

<div id="chatModal" class="modal">
    <div class="modal-contenido">
        <span class="cerrar">&times;</span>
        <h3 id="chatTitulo">Chat Activo</h3>
        
        <div id="chat-historial">
            Cargando historial...
        </div>
        
        <form id="formEnviarMensaje" class="chat-input-area">
            <input type="hidden" id="chatIdInput" name="id_chat">
            <textarea id="mensajeContenido" name="contenido" rows="2" required placeholder="Escribe tu respuesta..."></textarea>
            <button type="submit" class="boton-contacto">Enviar</button>
        </form>
    </div>
</div>