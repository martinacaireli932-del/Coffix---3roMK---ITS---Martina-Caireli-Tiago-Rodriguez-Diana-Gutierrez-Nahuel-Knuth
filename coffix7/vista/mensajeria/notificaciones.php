<?php

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php?ruta=iniciar_sesion');
    exit();
}
$es_proveedor = in_array($_SESSION['usuario']['rol'], ['Proveedor', 'Psicologo']);
$id_usuario_actual = $_SESSION['usuario']['id'];
?>

<div class="contenedor-notificaciones">
    <h2>🔔 Tus Notificaciones</h2>
    <p>Tienes <?php echo count($notificaciones); ?> notificaciones pendientes o recientes.</p>
    
    <?php if (empty($notificaciones)): ?>
        <p class="alerta alerta-info">No tienes notificaciones nuevas.</p>
    <?php else: ?>
        <?php foreach ($notificaciones as $notif): 
            $clase_extra = '';
            if ($notif['tipo'] === 'RECORDATORIO') {
                $clase_extra = 'notif-recordatorio';
            } elseif ($notif['tipo'] === 'SOLICITUD_RESERVA') {
                $clase_extra = 'notif-solicitud';
            } elseif (isset($notif['texto']) && strpos($notif['texto'], 'aceptada') !== false) {
                $clase_extra = 'notif-aceptada';
            } elseif (isset($notif['texto']) && strpos($notif['texto'], 'rechazada') !== false) {
                $clase_extra = 'notif-rechazada';
            }
        ?>
            <div class="notificacion-item <?php echo $clase_extra; ?>">
                <p class="notificacion-texto">
                    <span class="notificacion-fecha-hora"><?php echo date('d/m H:i', strtotime($notif['fecha'])); ?></span>
                    <?php 
                    echo str_replace(['**', '*'], ['<strong>', '</strong>', '<em>', '</em>'], $notif['texto']); 
                    ?>
                </p>
                
                <div class="notificacion-acciones">
                    
                    <?php if ($notif['tipo'] === 'SOLICITUD_RESERVA' && $es_proveedor): ?>
                        <button class="btn-aceptar" data-id-reserva="<?php echo $notif['id_reserva']; ?>">ACEPTAR</button>
                        <button class="btn-rechazar" data-id-reserva="<?php echo $notif['id_reserva']; ?>">RECHAZAR</button>
                        
                    <?php elseif ($notif['tipo'] === 'RESERVA_RESPUESTA'): ?>
                        <span class="estado-reserva">Acción realizada</span>
                        
                    <?php elseif ($notif['tipo'] === 'SOLICITUD_CHAT' && $es_proveedor): ?>
                        <button class="btn-aceptar" data-id-chat="<?php echo $notif['id_chat']; ?>">Aceptar Chat</button>
                        <button class="btn-rechazar" data-id-chat="<?php echo $notif['id_chat']; ?>">Rechazar Chat</button>
                        
                    <?php elseif (isset($notif['id_chat'])): ?>
                        <button class="btn-ver-chat" data-id-chat="<?php echo $notif['id_chat']; ?>">Ver Mensajes</button>
                        
                    <?php elseif ($notif['tipo'] === 'RECORDATORIO'): ?>
                        <span class="etiqueta-recordatorio">¡Cita Mañana!</span>
                        
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div id="chatModal" class="modal">
    <div class="modal-contenido">
        <span class="cerrar cerrar-chat-modal">&times;</span>
        <h3 id="chatTitulo">Cargando Chat...</h3>
        
        <div id="chat-historial">
            <div class="loading-message">Cargando historial...</div>
        </div>
        
        <form id="formEnviarMensaje" class="chat-input-area">
            <input type="hidden" id="chatIdInput" name="id_chat">
            <textarea id="mensajeContenido" name="contenido" rows="2" required placeholder="Escribe tu respuesta..."></textarea>
            <button type="submit" class="btn-aceptar">Enviar</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatModal = document.getElementById('chatModal');
    const closeChatSpan = document.querySelector('.cerrar-chat-modal');
    const formEnviarMensaje = document.getElementById('formEnviarMensaje');
    const historialContainer = document.getElementById('chat-historial');
    const chatIdInput = document.getElementById('chatIdInput');
    const chatTitulo = document.getElementById('chatTitulo');
    const idUsuarioActual = <?php echo json_encode($id_usuario_actual); ?>;

    function enviarPeticion(ruta, formData, successCallback) {
        fetch(`index.php?ruta=${ruta}`, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            alert(data.mensaje);
            if (data.exito) {
                if (successCallback) successCallback(data);
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error AJAX:', error);
            alert('Ocurrió un error de comunicación con el servidor. Revise la consola.');
        });
    }

    document.querySelectorAll('.btn-aceptar[data-id-reserva], .btn-rechazar[data-id-reserva]').forEach(button => {
        button.addEventListener('click', function() {
            const idReserva = this.getAttribute('data-id-reserva');
            const esAceptar = this.classList.contains('btn-aceptar');
            const accion = esAceptar ? 'aceptar_reserva' : 'rechazar_reserva';
            
            if (!confirm(`¿Estás seguro de que deseas ${esAceptar ? 'ACEPTAR' : 'RECHAZAR'} esta reserva?`)) return;

            const formData = new FormData();
            formData.append('id_reserva', idReserva);
            enviarPeticion(`procesar_${accion}`, formData); 
        });
    });

    document.querySelectorAll('.btn-aceptar[data-id-chat], .btn-rechazar[data-id-chat]').forEach(button => {
        button.addEventListener('click', function() {
            const idChat = this.getAttribute('data-id-chat');
            const esAceptar = this.classList.contains('btn-aceptar');
            const accion = esAceptar ? 'aceptar_chat' : 'rechazar_chat';
            
            if (!confirm(`¿Estás seguro de que deseas ${esAceptar ? 'ACEPTAR' : 'RECHAZAR'} este chat?`)) return;

            const formData = new FormData();
            formData.append('id_chat', idChat);

            enviarPeticion(`procesar_${accion}`, formData);
        });
    });
    
    function renderizarMensajes(mensajes) {
        historialContainer.innerHTML = '';
        if (mensajes.length === 0) {
            historialContainer.innerHTML = '<p class="chat-vacio">Aún no hay mensajes en este chat.</p>';
            return;
        }

        mensajes.forEach(msg => {
            const esMio = parseInt(msg.id_emisor) === parseInt(idUsuarioActual);
            const clase = esMio ? 'mensaje-emisor' : 'mensaje-receptor';
            
            const div = document.createElement('div');
            div.className = `mensaje-item ${clase}`;
            div.innerHTML = `
                <p>${msg.contenido}</p>
                <span class="chat-hora">${msg.nombre_emisor} - ${new Date(msg.fecha_envio).toLocaleTimeString()}</span>
            `;
            historialContainer.appendChild(div);
        });

        historialContainer.scrollTop = historialContainer.scrollHeight;
    }

    document.querySelectorAll('.btn-ver-chat').forEach(button => {
        button.addEventListener('click', function() {
            const idChat = this.getAttribute('data-id-chat');
            
            chatTitulo.textContent = 'Cargando Chat...';
            historialContainer.innerHTML = '<div class="loading-message">Cargando historial...</div>';
            chatIdInput.value = idChat;
            chatModal.style.display = 'block';

            fetch(`index.php?ruta=obtener_historial_chat&id_chat=${idChat}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exito) {
                        chatTitulo.textContent = `Chat con ${data.chat.nombre_otro_usuario}`;
                        renderizarMensajes(data.mensajes);
                    } else {
                        chatTitulo.textContent = 'Error al cargar chat';
                        historialContainer.innerHTML = `<p class="error">${data.mensaje}</p>`;
                    }
                })
                .catch(error => {
                    chatTitulo.textContent = 'Error de conexión';
                    historialContainer.innerHTML = `<p class="error">No se pudo conectar al servidor.</p>`;
                });
        });
    });
    
    if (closeChatSpan) {
        closeChatSpan.onclick = function() {
            chatModal.style.display = "none";
        }
    }
    window.addEventListener('click', function(event) {
        if (event.target == chatModal) {
            chatModal.style.display = "none";
        }
    });

    formEnviarMensaje.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const contenido = document.getElementById('mensajeContenido').value.trim();
        if (!contenido) return;

        const formData = new FormData(formEnviarMensaje);
        fetch('index.php?ruta=procesar_enviar_mensaje', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.exito) {
                document.getElementById('mensajeContenido').value = '';
                document.querySelector('.btn-ver-chat[data-id-chat="' + chatIdInput.value + '"]').click();
            } else {
                alert('Error al enviar mensaje: ' + data.mensaje);
            }
        })
        .catch(error => {
            console.error('Error al enviar mensaje:', error);
            alert('Error de conexión al enviar el mensaje.');
        });
    });

});
</script>