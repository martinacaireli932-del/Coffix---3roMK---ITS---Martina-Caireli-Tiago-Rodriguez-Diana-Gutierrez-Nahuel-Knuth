<?php
require_once 'conexionBD.php'; 
require_once 'modelo/ModeloServicio.php'; 

class ModeloReserva {
    private $pdo;
    private $modeloServicio;

    public function __construct() {
        $this->pdo = ModeloConexion::obtenerInstancia()->obtenerPDO();
        $this->modeloServicio = new ModeloServicio(); 
    } 

    public function existeReservaPendienteOActiva($id_servicio, $id_cliente) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM reservaciones 
                WHERE id_servicio = ? AND id_cliente = ? AND estado IN ('PENDIENTE', 'ACEPTADA')
            ");
            $stmt->execute([$id_servicio, $id_cliente]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar reserva existente: " . $e->getMessage());
            return true; 
        }
    }
    
    public function crearSolicitudReserva($id_servicio, $id_cliente, $fecha, $hora) {
        $estado = 'PENDIENTE';

        try {
            $stmt_servicio = $this->pdo->prepare("SELECT id_usuario FROM servicios WHERE id = ?");
            $stmt_servicio->execute([$id_servicio]);
            $id_proveedor = $stmt_servicio->fetchColumn();

            if (!$id_proveedor) {
                error_log("Error: No se encontró el proveedor para el servicio ID: " . $id_servicio);
                return false;
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO reservaciones (id_servicio, id_cliente, id_proveedor, fecha_reserva, hora_reserva, estado) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$id_servicio, $id_cliente, $id_proveedor, $fecha, $hora, $estado]);

        } catch (PDOException $e) {
            error_log("Error al crear solicitud de reserva: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarEstadoReserva($id_reserva, $estado) {
        $stmt = $this->pdo->prepare("UPDATE reservaciones SET estado = ? WHERE id = ?");
        return $stmt->execute([$estado, $id_reserva]);
    }

    public function obtenerNotificacionesReserva($id_usuario, $rol) {
        $notificaciones = [];
        
        $fecha_manana = date('Y-m-d', strtotime('+1 day'));

        $sql_recordatorios = "
            SELECT 
                r.id, r.id_cliente, r.id_proveedor, r.fecha_reserva, r.hora_reserva, r.fecha_solicitud,
                s.titulo AS nombre_servicio, 
                u_cliente.nombre AS nombre_cliente,
                u_proveedor.nombre AS nombre_proveedor
            FROM reservaciones r
            JOIN servicios s ON r.id_servicio = s.id 
            JOIN usuarios u_cliente ON r.id_cliente = u_cliente.id
            JOIN usuarios u_proveedor ON r.id_proveedor = u_proveedor.id
            WHERE r.estado = 'ACEPTADA' AND r.fecha_reserva = ? 
            AND (r.id_cliente = ? OR r.id_proveedor = ?)
        ";

        try {
            $stmt_rec = $this->pdo->prepare($sql_recordatorios);
            $stmt_rec->execute([$fecha_manana, $id_usuario, $id_usuario]); 
            $recordatorios_db = $stmt_rec->fetchAll(PDO::FETCH_ASSOC);

            foreach ($recordatorios_db as $row) {
                $hora_formato = substr($row['hora_reserva'], 0, 5);

                if ($row['id_cliente'] == $id_usuario) {
                    $notificaciones[] = [
                        'tipo' => 'RECORDATORIO',
                        'id_reserva' => $row['id'],
                        'texto' => "🔔 **Recordatorio:** No olvide su reservación para el servicio '{$row['nombre_servicio']}' a las {$hora_formato} del día de mañana!",
                        'fecha' => $row['fecha_solicitud'],
                        'es_importante' => true 
                    ];
                }
                
                if ($row['id_proveedor'] == $id_usuario) {
                    $notificaciones[] = [
                        'tipo' => 'RECORDATORIO',
                        'id_reserva' => $row['id'],
                        'texto' => "🔔 **Recordatorio de Cita:** Mañana tiene una reservación para su servicio '{$row['nombre_servicio']}' del cliente **{$row['nombre_cliente']}** a las {$hora_formato}.",
                        'fecha' => $row['fecha_solicitud'],
                        'es_importante' => true 
                    ];
                }
            }
        } catch (PDOException $e) {
            error_log("Error al generar recordatorios: " . $e->getMessage());
        }


        if ($rol === 'Proveedor' || $rol === 'Psicologo') {
            $stmt = $this->pdo->prepare("
                SELECT 
                    r.id, r.fecha_reserva, r.hora_reserva, r.fecha_solicitud, r.estado,
                    s.titulo AS nombre_servicio, u.nombre AS nombre_cliente
                FROM reservaciones r
                JOIN servicios s ON r.id_servicio = s.id 
                JOIN usuarios u ON r.id_cliente = u.id
                WHERE r.id_proveedor = ? AND r.estado = 'PENDIENTE'
                ORDER BY r.fecha_solicitud DESC
            ");
            $stmt->execute([$id_usuario]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); 

            foreach ($result as $row) {
                $notificaciones[] = [
                    'tipo' => 'SOLICITUD_RESERVA',
                    'id_reserva' => $row['id'],
                    'texto' => "Nueva solicitud de reserva de **{$row['nombre_cliente']}** para el '{$row['nombre_servicio']}' el {$row['fecha_reserva']} a las " . substr($row['hora_reserva'], 0, 5) . ".",
                    'fecha' => $row['fecha_solicitud'],
                    'es_importante' => true 
                ];
            }
        }
        
        if ($rol === 'Cliente') {
            $stmt = $this->pdo->prepare("
                SELECT 
                    r.id, r.fecha_reserva, r.hora_reserva, r.fecha_solicitud, r.estado,
                    s.titulo AS nombre_servicio
                FROM reservaciones r
                JOIN servicios s ON r.id_servicio = s.id 
                WHERE r.id_cliente = ? AND r.estado IN ('ACEPTADA', 'RECHAZADA')
                ORDER BY r.fecha_solicitud DESC
            ");
            $stmt->execute([$id_usuario]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($result as $row) {
                $estado = $row['estado'];
                $accion = ($estado === 'ACEPTADA') ? 'aceptada' : 'rechazada';
                $notificaciones[] = [
                    'tipo' => 'RESERVA_RESPUESTA',
                    'id_reserva' => $row['id'],
                    'texto' => "Tu reserva para el servicio '{$row['nombre_servicio']}' el {$row['fecha_reserva']} ha sido **{$accion}**.",
                    'fecha' => $row['fecha_solicitud'],
                    'es_importante' => false
                ];
            }
        }

        usort($notificaciones, function($a, $b) {
            return strtotime($b['fecha']) - strtotime($a['fecha']);
        });

        return $notificaciones;
    }
}