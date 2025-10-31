<?php
require_once 'modelo/ModeloCalendario.php'; 

class ControladorCalendario {
    private $modeloCalendario;

    public function __construct() {
        $this->modeloCalendario = new ModeloCalendario();
    }

    public function cargarVistaCalendario() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?ruta=iniciar_sesion');
            exit();
        }
        
        require_once 'vista/plantilla/encabezado.php';
        require_once 'vista/calendario/vista_calendario.php'; 
        require_once 'vista/plantilla/pie_pagina.php';
    }

    public function obtenerEventosCalendario() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['usuario'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }
        
        $id_usuario = $_SESSION['usuario']['id'];
        $rol = $_SESSION['usuario']['rol'];
        
        $reservas = $this->modeloCalendario->obtenerReservasAceptadas($id_usuario, $rol);
        $eventos = [];

        foreach ($reservas as $reserva) {
            
            if ($rol === 'Cliente') {
                $title = "Cita con {$reserva['otra_persona_nombre']} {$reserva['otra_persona_apellido']}";
                $color = '#28a745';
            } else {
                $title = "Reserva: {$reserva['otra_persona_nombre']} ({$reserva['titulo_servicio']})";
                $color = '#007bff';
            }

            $eventos[] = [
                'id' => $reserva['id'],
                'title' => $title,
                'start' => "{$reserva['fecha_reserva']}T{$reserva['hora_reserva']}",
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'servicio_titulo' => $reserva['titulo_servicio'],
                    'otra_persona_nombre' => "{$reserva['otra_persona_nombre']} {$reserva['otra_persona_apellido']}",
                    'ubicacion' => $reserva['ubicacion']
                ]
            ];
        }

        echo json_encode($eventos);
        exit();
    }
}
?>