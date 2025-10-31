<?php
require_once 'conexionBD.php'; 

class ModeloMensajeria {
    private $db;

    public function __construct() {
        $instancia_conexion = ModeloConexion::obtenerInstancia();
        $this->db = $instancia_conexion->obtenerPDO();
    }

    public function crearSolicitudChat($id_servicio, $id_cliente, $id_proveedor) {
        try {
            $stmt = $this->db->prepare("SELECT id FROM chat WHERE id_servicio = ? AND id_cliente = ? AND estado IN ('PENDIENTE', 'ACTIVO')");
            $stmt->execute([$id_servicio, $id_cliente]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return $result['id'];
            }

            $stmt = $this->db->prepare("INSERT INTO chat (id_servicio, id_cliente, id_proveedor, estado) VALUES (?, ?, ?, 'PENDIENTE')");
            if ($stmt->execute([$id_servicio, $id_cliente, $id_proveedor])) {
                return $this->db->lastInsertId(); 
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al crear solicitud de chat: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarEstadoChat($id_chat, $estado) {
        $stmt = $this->db->prepare("UPDATE chat SET estado = ? WHERE id = ?");
        return $stmt->execute([$estado, $id_chat]); 
    }
    
    public function obtenerChatPorId($id_chat) {
        $stmt = $this->db->prepare("
            SELECT 
                c.id, c.id_servicio, c.id_cliente, c.id_proveedor, c.estado,
                s.titulo AS nombre_servicio,
                uc.nombre AS nombre_cliente,
                up.nombre AS nombre_proveedor
            FROM chat c
            JOIN servicios s ON c.id_servicio = s.id 
            JOIN usuarios uc ON c.id_cliente = uc.id
            JOIN usuarios up ON c.id_proveedor = up.id
            WHERE c.id = ?
        ");
        $stmt->execute([$id_chat]);
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    public function enviarMensaje($id_chat, $id_emisor, $contenido) {
        $stmt = $this->db->prepare("INSERT INTO mensaje (id_chat, id_emisor, contenido) VALUES (?, ?, ?)");
        return $stmt->execute([$id_chat, $id_emisor, $contenido]);
    }

    public function obtenerMensajesPorChat($id_chat) {
        $stmt = $this->db->prepare("
            SELECT 
                m.id, m.id_emisor, m.contenido, m.fecha_envio, u.nombre AS nombre_emisor
            FROM mensaje m
            JOIN usuarios u ON m.id_emisor = u.id
            WHERE m.id_chat = ?
            ORDER BY m.fecha_envio ASC
        ");
        $stmt->execute([$id_chat]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }
    
    public function marcarMensajesLeidos($id_chat, $id_receptor) {
        $stmt = $this->db->prepare("UPDATE mensaje m 
                                     JOIN chat c ON m.id_chat = c.id
                                     SET m.leido = TRUE 
                                     WHERE m.id_chat = ? 
                                     AND m.id_emisor != ? 
                                     AND m.leido = FALSE");
        return $stmt->execute([$id_chat, $id_receptor]);
    }

    public function obtenerNotificacionesMensajeria($id_usuario) {
        $solicitudes = [];
        $rechazos = [];
        $mensajes_nuevos = [];

        $stmt = $this->db->prepare("
            SELECT 
                c.id, c.id_cliente, c.id_servicio, c.estado, c.fecha_creacion,
                u.nombre AS nombre_cliente, s.titulo AS nombre_servicio
            FROM chat c
            JOIN usuarios u ON c.id_cliente = u.id
            JOIN servicios s ON c.id_servicio = s.id 
            WHERE c.id_proveedor = ? AND c.estado = 'PENDIENTE'
            ORDER BY c.fecha_creacion DESC
        ");
        $stmt->execute([$id_usuario]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC); 
        foreach ($result as $row) {
            $solicitudes[] = [
                'tipo' => 'SOLICITUD_CHAT',
                'id_chat' => $row['id'],
                'texto' => "Nueva solicitud de chat de **{$row['nombre_cliente']}** por el servicio '{$row['nombre_servicio']}'.",
                'fecha' => $row['fecha_creacion']
            ];
        }

        $stmt = $this->db->prepare("
            SELECT 
                c.id, c.id_proveedor, c.id_servicio, c.fecha_creacion,
                u.nombre AS nombre_proveedor, s.titulo AS nombre_servicio
            FROM chat c
            JOIN usuarios u ON c.id_proveedor = u.id
            JOIN servicios s ON c.id_servicio = s.id 
            WHERE c.id_cliente = ? AND c.estado = 'RECHAZADO'
            ORDER BY c.fecha_creacion DESC
        ");
        $stmt->execute([$id_usuario]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $rechazos[] = [
                'tipo' => 'CHAT_RECHAZADO',
                'id_chat' => $row['id'],
                'texto' => "El proveedor **{$row['nombre_proveedor']}** rechazó tu solicitud de chat por el servicio '{$row['nombre_servicio']}'.",
                'fecha' => $row['fecha_creacion']
            ];
        }

        $stmt = $this->db->prepare("
            SELECT 
                m.id, m.id_chat, m.fecha_envio, m.contenido,
                u_emisor.nombre AS nombre_emisor,
                s.titulo AS nombre_servicio
            FROM mensaje m
            JOIN chat c ON m.id_chat = c.id
            JOIN usuarios u_emisor ON m.id_emisor = u_emisor.id
            JOIN servicios s ON c.id_servicio = s.id 
            WHERE (c.id_cliente = ? OR c.id_proveedor = ?) 
            AND m.id_emisor != ? 
            AND m.leido = FALSE 
            AND c.estado = 'ACTIVO'
            ORDER BY m.fecha_envio DESC
        ");
        $stmt->execute([$id_usuario, $id_usuario, $id_usuario]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $mensajes_nuevos[] = [
                'tipo' => 'NUEVO_MENSAJE',
                'id_chat' => $row['id_chat'],
                'texto' => "Nuevo mensaje de **{$row['nombre_emisor']}** sobre el servicio '{$row['nombre_servicio']}'.",
                'fecha' => $row['fecha_envio']
            ];
        }
        
        return array_merge($solicitudes, $rechazos, $mensajes_nuevos);
    }
}
?>