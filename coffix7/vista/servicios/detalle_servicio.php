<?php
if (!isset($servicio) || empty($servicio)) {
    echo '<section class="contenedor-detalle"><h2>Servicio no encontrado.</h2></section>';
    return;
}
$esta_logueado = isset($_SESSION['usuario']);
$id_usuario_actual = $_SESSION['usuario']['id'] ?? null;
$id_propietario = $servicio['id_usuario'] ?? null;
$es_propietario = $esta_logueado && ($id_usuario_actual == $id_propietario);
$puede_interactuar = $esta_logueado && !$es_propietario;

$total_reseñas = isset($reseñas) ? count($reseñas) : 0;
$ya_ha_comentado = false;
if ($esta_logueado && isset($reseñas)) {
    foreach ($reseñas as $reseña) {
        if ($reseña['id_usuario'] == $id_usuario_actual) {
            $ya_ha_comentado = true;
            break;
        }
    }
}
$ruta_imagen_principal = $servicio['imagenes'] ?? '';
$ruta_foto_perfil = $servicio['foto_perfil'] ?? 'recursos/img/default/default-perfil.png';
$id_proveedor = $servicio['id_usuario'] ?? null;

$promedio_proveedor_formato = $servicio['promedio_proveedor_formato'] ?? '0.0';
?>

<section class="contenedor-detalle-servicio">
    <h2 class="titulo-servicio-detalle"><?php echo htmlspecialchars($servicio['titulo'] ?? 'Servicio sin título'); ?></h2>
    
    <div class="detalle-encabezado">
        <div class="servicio-meta-publicacion">
            <img src="<?php echo htmlspecialchars($ruta_foto_perfil); ?>" alt="Foto de Perfil" class="foto-publicador-grande">
            
            <span class="nombre-publicador">
                Publicado por: 
                <strong>
                    <?php echo htmlspecialchars($servicio['nombre_usuario'] ?? 'Usuario Desconocido'); ?> 
                    (<?php echo htmlspecialchars($servicio['rol'] ?? 'N/A'); ?>)
                </strong>
                
                <span class="calificacion-proveedor" title="Calificación promedio del proveedor en todos sus servicios">
                    | Calificación Proveedor: 
                    <strong style="color: gold; font-size: 1.1em;"><?php echo $promedio_proveedor_formato; ?> ⭐</strong>
                </span>
                </span>
            
            <span class="fecha-publicacion">Fecha: <?php echo date('d/m/Y', strtotime($servicio['fecha_publicacion'] ?? 'now')); ?></span>
        </div>
        <div class="servicio-acciones">
            <?php if ($es_propietario): ?>
                <a href="index.php?ruta=editar_servicio&id=<?php echo $servicio['id']; ?>" class="boton-editar">✏️ Editar</a>
                <form action="index.php?ruta=procesar_eliminacion_servicio" method="POST" style="display:inline;" onsubmit="return confirm('¿Confirmas que deseas eliminar este servicio?');">
                    <input type="hidden" name="id_servicio" value="<?php echo $servicio['id']; ?>">
                    <button type="submit" class="boton-eliminar-servicio">🗑️ Eliminar</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="detalle-contenido">
        <div class="servicio-galeria">
            <?php if (!empty($ruta_imagen_principal)): ?>
                <img src="<?php echo htmlspecialchars($ruta_imagen_principal); ?>" alt="Imagen principal del servicio" class="imagen-principal-detalle">
                <?php else: ?>
                <img src="recursos/img/servicios/default.png" alt="Sin imagen" class="imagen-principal-detalle">
            <?php endif; ?>
        </div>
        
        <div class="servicio-informacion">
            <h3>Descripción:</h3>
            <p><?php echo nl2br(htmlspecialchars($servicio['descripcion'] ?? 'Sin descripción.')); ?></p>
            
            <hr>
            
            <div class="detalle-especifico">
                <p><strong>Categoría:</strong> <?php echo htmlspecialchars($servicio['categoria'] ?? 'N/A'); ?></p>
                <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($servicio['ubicacion'] ?? 'N/A'); ?></p>
                <p><strong>Tipo de Servicio:</strong> <?php echo htmlspecialchars($servicio['disponibilidad'] ?? 'N/A'); ?></p> 
                
                <p class="precio-final">Precio: <strong>$<?php echo htmlspecialchars($servicio['precio'] ?? '0.00'); ?></strong></p>
                
                <p class="calificacion-final">Calificación del Servicio: 
                    <strong><?php echo number_format($servicio['calificacion_promedio'] ?? 0, 1); ?> ⭐</strong>
                    (<?php echo $total_reseñas; ?> Reseñas)
                </p>
            </div>
            
            <div class="acciones-cliente">
                <?php if ($puede_interactuar): ?>
                    
                    <button type="button" id="abrirReservaModal" class="boton-reserva">
                        📅 Solicitar Reserva Servicio
                    </button>
                    
                    <button type="button" id="abrirChatModal" class="boton-contacto">
                        ✉️ Enviar Mensaje al Proveedor
                    </button>
                
                <?php elseif (!$esta_logueado): ?>
                    <p class="alerta">Debes <a href="index.php?ruta=iniciar_sesion">iniciar sesión</a> para reservar o contactar al proveedor.</p>
                
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <hr id="reseñas">

    <div class="seccion-reseñas">
        <h3>💬 Reseñas y Comentarios</h3>
        
        <?php 
        if ($puede_interactuar && !$ya_ha_comentado): 
        ?>
            <?php require 'vista/reseñas/crear_reseña.php'; ?>
        <?php elseif ($es_propietario): ?>
            <p class="alerta">No puedes calificar tu propio servicio.</p>
        <?php elseif ($ya_ha_comentado): ?>
            <p class="alerta">Ya has dejado una reseña para este servicio. ¡Gracias!</p>
        <?php elseif (!$esta_logueado): ?>
            <p class="alerta">Debes <a href="index.php?ruta=iniciar_sesion">iniciar sesión</a> para dejar una reseña.</p>
        <?php endif; ?>

        <?php 
        if (isset($reseñas) && !empty($reseñas)):
            require 'vista/reseñas/lista_reseñas_servicio.php'; 
        else:
            echo '<p class="alerta">Aún no hay reseñas para este servicio.</p>';
        endif;
        ?>
    </div>
</section>

<?php 
require_once 'vista/reservas/reserva_modal.php'; 
require_once 'vista/mensajeria/chat_modal.php'; 
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const idServicio = '<?php echo $servicio['id'] ?? ''; ?>';
        const idProveedor = '<?php echo $id_proveedor; ?>'; 

        function setupModal(modalId, openBtnId, formId, ajaxUrl, successMessage, onOpenCallback = null) {
            const modal = document.getElementById(modalId);
            const btn = document.getElementById(openBtnId);
            const closeSpan = modal ? modal.getElementsByClassName('cerrar')[0] : null;
            const form = document.getElementById(formId);

            if (btn && modal) {
                btn.onclick = function() {
                    modal.style.display = "block";
                    if (onOpenCallback) {
                        onOpenCallback();
                    }
                }
            }

            if (closeSpan) {
                closeSpan.onclick = function() { modal.style.display = "none"; }
            }
            window.addEventListener('click', function(event) {
                if (event.target == modal) { modal.style.display = "none"; }
            });

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(form);
                    
                    fetch(ajaxUrl, {
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
                        modal.style.display = "none";
                        
                        if (data.exito) {
                            alert(successMessage + (data.mensaje || 'Éxito.'));
                        } else {
                            alert('❌ Error: ' + (data.mensaje || 'Ocurrió un error.'));
                        }
                        
                        window.location.href = 'index.php?ruta=detalle_servicio&id=' + idServicio;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ocurrió un error de comunicación con el servidor. Revise la consola para detalles.');
                    });
                });
            }
        }
        const calendarioDiv = document.getElementById('calendarioDisponibilidad');

        function cargarDisponibilidad(idProveedor) {
            if (!idProveedor || !calendarioDiv) {
                return;
            }
            calendarioDiv.innerHTML = 'Cargando disponibilidad...';

            fetch(`index.php?ruta=obtener_disponibilidad_json&id_proveedor=${idProveedor}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Fallo al obtener la disponibilidad del servidor.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.exito && data.data && data.data.length > 0) {
                        renderizarDisponibilidad(data.data);
                    } else {
                        calendarioDiv.innerHTML = '<p class="alerta">El proveedor no ha definido su horario de trabajo o está inactivo.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error al cargar disponibilidad:', error);
                    calendarioDiv.innerHTML = '<p class="error">Error al conectar con el servidor para obtener la disponibilidad.</p>';
                });
        }
        
        function renderizarDisponibilidad(disponibilidad) {
            let html = '<ul class="lista-disponibilidad-cliente">';
            
            const diasOrden = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            const horarios = {};
            
            disponibilidad.forEach(item => {
                horarios[item.dia_semana] = `${item.hora_inicio.substring(0, 5)} - ${item.hora_fin.substring(0, 5)}`;
            });
            
            diasOrden.forEach(dia => {
                const horario = horarios[dia];
                html += `<li><strong>${dia}:</strong> `;
                if (horario) {
                    html += `<span class="disponible">${horario}</span></li>`;
                } else {
                    html += `<span class="no-disponible">No disponible</span></li>`;
                }
            });
            
            html += '</ul>';
            calendarioDiv.innerHTML = html;
        }
        setupModal(
            'reservaModal', 
            'abrirReservaModal', 
            'formSolicitudReserva', 
            'index.php?ruta=procesar_solicitud_reserva',
            '✅ Solicitud de Reserva: ',
            function() { 
                cargarDisponibilidad(idProveedor);
            }
        );
        setupModal(
            'solicitudChatModal', 
            'abrirChatModal', 
            'formSolicitudChat', 
            'index.php?ruta=procesar_solicitud_chat',
            '✅ Solicitud de Chat: '
        );
    });
</script>