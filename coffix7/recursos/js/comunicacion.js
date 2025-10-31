document.addEventListener('DOMContentLoaded', function() {
    const userId = document.querySelector('body').dataset.userId;
    if (!userId) return;

    function mostrarAlerta(mensaje, tipo = 'exito') {
        alert(mensaje);
    }

    const modalReserva = document.getElementById('reservaModal');
    const btnReserva = document.getElementById('btnSolicitarReserva');
    const formReserva = document.getElementById('formSolicitudReserva');

    if (btnReserva) {
        btnReserva.onclick = function() {
            modalReserva.style.display = 'block';
        }
    }

    if (formReserva) {
        formReserva.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('index.php?ruta=procesar_solicitud_reserva', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                mostrarAlerta(data.mensaje, data.exito ? 'exito' : 'error');
                if (data.exito) {
                    modalReserva.style.display = 'none';
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    const modalChat = document.getElementById('chatModal');
    const formSolicitudChat = document.getElementById('formSolicitudChat');
    const formEnviarMensaje = document.getElementById('formEnviarMensaje');
    let chatActivoId = null;

    document.getElementById('btnEnviarMensaje')?.addEventListener('click', function() {
        document.getElementById('solicitudChatModal').style.display = 'block';
    });

    if (formSolicitudChat) {
        formSolicitudChat.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('index.php?ruta=procesar_solicitud_chat', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                mostrarAlerta(data.mensaje, data.exito ? 'exito' : 'error');
                if (data.exito) {
                    document.getElementById('solicitudChatModal').style.display = 'none';
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }
    function cargarHistorialChat(idChat) {
        chatActivoId = idChat;
        modalChat.style.display = 'block';
        document.getElementById('chatIdInput').value = idChat;

        fetch(`index.php?ruta=obtener_historial_chat&id_chat=${idChat}`)
            .then(res => res.json())
            .then(data => {
                if (data.exito) {
                    const historialDiv = document.getElementById('chat-historial');
                    historialDiv.innerHTML = '';
                    
                    document.getElementById('chatTitulo').textContent = `Chat: ${data.chat.nombre_servicio}`;

                    data.mensajes.forEach(msg => {
                        const esEmisor = msg.id_emisor == data.id_usuario_actual;
                        const className = esEmisor ? 'mensaje-emisor' : 'mensaje-receptor';
                        const nombreEmisor = esEmisor ? 'Tú' : msg.nombre_emisor;

                        const msgDiv = document.createElement('div');
                        msgDiv.className = `mensaje-item ${className}`;
                        msgDiv.innerHTML = `<strong>${nombreEmisor}</strong>: ${msg.contenido} <br> <small>${msg.fecha_envio}</small>`;
                        historialDiv.appendChild(msgDiv);
                    });
                    
                    historialDiv.scrollTop = historialDiv.scrollHeight;
                } else {
                    mostrarAlerta(data.mensaje, 'error');
                }
            })
            .catch(error => console.error('Error al cargar chat:', error));
    }

    if (formEnviarMensaje) {
        formEnviarMensaje.addEventListener('submit', function(e) {
            e.preventDefault();
            const contenido = document.getElementById('mensajeContenido').value;
            if (!contenido.trim()) return;

            const formData = new FormData(this);
            formData.append('id_chat', chatActivoId);
            
            fetch('index.php?ruta=procesar_enviar_mensaje', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.exito) {
                    document.getElementById('mensajeContenido').value = '';
                    cargarHistorialChat(chatActivoId);
                } else {
                    mostrarAlerta(data.mensaje, 'error');
                }
            })
            .catch(error => console.error('Error al enviar mensaje:', error));
        });
    }

    document.querySelectorAll('.notificacion-item').forEach(item => {
        item.addEventListener('click', function(e) {
            if (e.target.closest('.btn-aceptar') || e.target.closest('.btn-rechazar')) {
                const esReserva = e.target.closest('[data-id-reserva]');
                const id = esReserva ? e.target.closest('[data-id-reserva]').dataset.idReserva : e.target.closest('[data-id-chat]').dataset.idChat;
                const ruta = e.target.closest('.btn-aceptar') ? (esReserva ? 'procesar_aceptar_reserva' : 'procesar_aceptar_chat') : (esReserva ? 'procesar_rechazar_reserva' : 'procesar_rechazar_chat');
                
                const formData = new FormData();
                formData.append(esReserva ? 'id_reserva' : 'id_chat', id);
                
                fetch(`index.php?ruta=${ruta}`, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    mostrarAlerta(data.mensaje, data.exito ? 'exito' : 'error');
                    if (data.exito) {
                        window.location.reload(); 
                        if (ruta === 'procesar_aceptar_chat') {
                            cargarHistorialChat(data.id_chat);
                        }
                    }
                })
                .catch(error => console.error('Error de acción:', error));
            }

            if (e.target.closest('.btn-ver-chat')) {
                const idChat = e.target.closest('[data-id-chat]').dataset.idChat;
                cargarHistorialChat(idChat);
            }
        });
    });

    document.querySelectorAll('.cerrar').forEach(btn => {
        btn.onclick = function() {
            this.closest('.modal').style.display = 'none';
        }
    });
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
});