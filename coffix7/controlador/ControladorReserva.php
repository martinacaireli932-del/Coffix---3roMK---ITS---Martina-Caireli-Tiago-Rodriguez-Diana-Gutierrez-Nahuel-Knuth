<?php
require_once 'modelo/ModeloReserva.php';
require_once 'modelo/ModeloServicio.php'; 

class ControladorReserva {
    private $modeloReserva;

    public function __construct() {
        $this->modeloReserva = new ModeloReserva();
    }

    public function procesarSolicitudReserva() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] === 'Administrador') {
            echo json_encode(['exito' => false, 'mensaje' => 'Petición inválida, usuario no logueado o rol no permitido.']);
            exit();
        }

        $id_cliente = $_SESSION['usuario']['id'];
        $id_servicio = $_POST['id_servicio'] ?? null;
        $fecha = $_POST['fecha'] ?? null;
        $hora = $_POST['hora'] ?? null;

        if (empty($id_servicio) || empty($fecha) || empty($hora)) {
            echo json_encode(['exito' => false, 'mensaje' => 'Debes especificar una fecha y hora.']);
            exit();
        }
        
        if (strtotime($fecha) < strtotime(date('Y-m-d'))) {
            echo json_encode(['exito' => false, 'mensaje' => 'La fecha de reserva debe ser futura o el día de hoy.']);
            exit();
        }

        if ($this->modeloReserva->existeReservaPendienteOActiva($id_servicio, $id_cliente)) {
            echo json_encode(['exito' => false, 'mensaje' => 'Ya tienes una solicitud de reserva PENDIENTE o ACEPTADA para este servicio.']);
            exit();
        }

        if ($this->modeloReserva->crearSolicitudReserva($id_servicio, $id_cliente, $fecha, $hora)) {
            echo json_encode(['exito' => true, 'mensaje' => 'Solicitud de reserva enviada con éxito. El proveedor será notificado.']);
        } else {
            echo json_encode(['exito' => false, 'mensaje' => 'Error al guardar la solicitud en la base de datos. Verifique logs del servidor.']);
        }
    }

    public function procesarAceptarReserva() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['rol'] !== 'Proveedor' && $_SESSION['usuario']['rol'] !== 'Psicologo') || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_reserva'])) {
            echo json_encode(['exito' => false, 'mensaje' => 'Acción no permitida o datos incompletos.']);
            exit();
        }
        
        $id_reserva = (int)$_POST['id_reserva'];
        
        if ($this->modeloReserva->actualizarEstadoReserva($id_reserva, 'ACEPTADA')) {
            echo json_encode(['exito' => true, 'mensaje' => '✅ Reserva aceptada con éxito y visible en el calendario.']);
        } else {
            echo json_encode(['exito' => false, 'mensaje' => '❌ Error al actualizar el estado de la reserva.']);
        }
    }

    public function procesarRechazarReserva() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['rol'] !== 'Proveedor' && $_SESSION['usuario']['rol'] !== 'Psicologo') || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_reserva'])) {
            echo json_encode(['exito' => false, 'mensaje' => 'Acción no permitida o datos incompletos.']);
            exit();
        }
        
        $id_reserva = (int)$_POST['id_reserva'];
        
        if ($this->modeloReserva->actualizarEstadoReserva($id_reserva, 'RECHAZADA')) {
            echo json_encode(['exito' => true, 'mensaje' => '✅ Reserva rechazada con éxito.']);
        } else {
            echo json_encode(['exito' => false, 'mensaje' => '❌ Error al actualizar el estado de la reserva.']);
        }
    }

    public function mostrarFormularioReserva() {
        header('Location: index.php?ruta=inicio');
        exit();
    }
}
?>