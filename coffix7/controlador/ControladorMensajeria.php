<?php
require_once 'modelo/ModeloMensajeria.php';
require_once 'modelo/ModeloReserva.php';

class ControladorMensajeria {
    private $modeloMensajeria;
    private $modeloReserva;

    public function __construct() {
        $this->modeloMensajeria = new ModeloMensajeria();
        $this->modeloReserva = new ModeloReserva();
    }

    public function iniciarChat() {
        header('Location: index.php?ruta=notificaciones');
        exit();
    }

    public function mostrarNotificaciones() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?ruta=iniciar_sesion');
            exit();
        }
        
        $id_usuario = $_SESSION['usuario']['id'];
        $rol = $_SESSION['usuario']['rol'];

        $notificaciones_mensajeria = $this->modeloMensajeria->obtenerNotificacionesMensajeria($id_usuario);
        
        $notificaciones_reserva = $this->modeloReserva->obtenerNotificacionesReserva($id_usuario, $rol);
        
        $notificaciones = array_merge($notificaciones_mensajeria, $notificaciones_reserva);
        
        usort($notificaciones, function($a, $b) {
            return strtotime($b['fecha']) - strtotime($a['fecha']);
        });

        require_once 'vista/plantilla/encabezado.php';
        require_once 'vista/mensajeria/notificaciones.php'; 
        require_once 'vista/plantilla/pie_pagina.php';
    }
    
    public function procesarSolicitudChat() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['usuario']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_servicio']) || !isset($_POST['id_proveedor']) || !isset($_POST['primer_mensaje'])) {
            echo json_encode(['exito' => false, 'mensaje' => 'Petición inválida o faltan datos.']);
            exit();
        }

        $id_cliente = $_SESSION['usuario']['id'];
        $id_servicio = (int)$_POST['id_servicio'];
        $id_proveedor = (int)$_POST['id_proveedor']; 
        $primer_mensaje = trim(htmlspecialchars($_POST['primer_mensaje']));

        if ($id_cliente == $id_proveedor) {
            echo json_encode(['exito' => false, 'mensaje' => 'No puedes iniciar un chat contigo mismo.']);
            exit();
        }
        
        if (empty($primer_mensaje)) {
            echo json_encode(['exito' => false, 'mensaje' => 'El mensaje inicial no puede estar vacío.']);
            exit();
        }

        $id_chat = $this->modeloMensajeria->crearSolicitudChat($id_servicio, $id_cliente, $id_proveedor);

        if ($id_chat) {
            $this->modeloMensajeria->enviarMensaje($id_chat, $id_cliente, $primer_mensaje);

            echo json_encode([
                'exito' => true, 
                'mensaje' => 'Solicitud de chat enviada. El proveedor debe aprobarlo.',
                'id_chat' => $id_chat
            ]);
        } else {
            echo json_encode(['exito' => false, 'mensaje' => 'Error al crear la solicitud de chat.']);
        }
    }
    
    public function procesarAceptarChat() {
        header('Content-Type: application/json');

        $es_proveedor_valido = isset($_SESSION['usuario']) && 
                             ($_SESSION['usuario']['rol'] === 'Proveedor' || $_SESSION['usuario']['rol'] === 'Psicologo');

        if (!$es_proveedor_valido || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_chat'])) {
            echo json_encode(['exito' => false, 'mensaje' => 'Acción no permitida.']);
            exit();
        }
        
        $id_chat = (int)$_POST['id_chat'];
        
        if ($this->modeloMensajeria->actualizarEstadoChat($id_chat, 'ACTIVO')) {
            echo json_encode(['exito' => true, 'mensaje' => 'Chat activado.', 'id_chat' => $id_chat]);
        } else {
            echo json_encode(['exito' => false, 'mensaje' => 'Error al activar el chat.']);
        }
    }

    public function procesarRechazarChat() {
        header('Content-Type: application/json');
        
        $es_proveedor_valido = isset($_SESSION['usuario']) && 
                             ($_SESSION['usuario']['rol'] === 'Proveedor' || $_SESSION['usuario']['rol'] === 'Psicologo');

        if (!$es_proveedor_valido || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_chat'])) {
            echo json_encode(['exito' => false, 'mensaje' => 'Acción no permitida.']);
            exit();
        }
        
        $id_chat = (int)$_POST['id_chat'];
        
        if ($this->modeloMensajeria->actualizarEstadoChat($id_chat, 'RECHAZADO')) {
            echo json_encode(['exito' => true, 'mensaje' => 'Chat rechazado.']);
        } else {
            echo json_encode(['exito' => false, 'mensaje' => 'Error al rechazar el chat.']);
        }
    }

    public function procesarEnviarMensaje() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['usuario']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_chat']) || !isset($_POST['contenido'])) {
            echo json_encode(['exito' => false, 'mensaje' => 'Petición inválida.']);
            exit();
        }

        $id_chat = (int)$_POST['id_chat'];
        $id_emisor = $_SESSION['usuario']['id'];
        $contenido = trim(htmlspecialchars($_POST['contenido']));

        if (empty($contenido)) {
            echo json_encode(['exito' => false, 'mensaje' => 'El mensaje no puede estar vacío.']);
            exit();
        }

        if ($this->modeloMensajeria->enviarMensaje($id_chat, $id_emisor, $contenido)) {
            echo json_encode(['exito' => true, 'mensaje' => 'Mensaje enviado.']);
        } else {
            echo json_encode(['exito' => false, 'mensaje' => 'Error al enviar el mensaje.']);
        }
    }

    public function obtenerHistorialChat() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['usuario']) || $_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id_chat'])) {
            echo json_encode(['exito' => false, 'mensaje' => 'Petición inválida.']);
            exit();
        }

        $id_chat = (int)$_GET['id_chat'];
        $id_usuario = $_SESSION['usuario']['id'];
        
        $chat = $this->modeloMensajeria->obtenerChatPorId($id_chat);
        
        if (!$chat || ((int)$chat['id_cliente'] != (int)$id_usuario && (int)$chat['id_proveedor'] != (int)$id_usuario)) {
                echo json_encode(['exito' => false, 'mensaje' => 'Acceso denegado o chat no encontrado.']);
                exit();
        }
        
        $mensajes = $this->modeloMensajeria->obtenerMensajesPorChat($id_chat);
        
        $this->modeloMensajeria->marcarMensajesLeidos($id_chat, $id_usuario);
        
        echo json_encode([
            'exito' => true, 
            'chat' => $chat,
            'mensajes' => $mensajes,
            'id_usuario_actual' => $id_usuario
        ]);
    }
}
?>