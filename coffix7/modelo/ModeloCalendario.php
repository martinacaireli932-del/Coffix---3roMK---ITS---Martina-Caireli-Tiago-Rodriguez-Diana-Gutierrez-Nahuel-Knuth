<?php
require_once 'conexionBD.php'; 

class ModeloCalendario {
    private $pdo; 

    public function __construct() {
        $this->pdo = ModeloConexion::obtenerInstancia()->obtenerPDO();
    } 

    public function obtenerReservasAceptadas($id_usuario, $rol) {
        try {
            $sql = "
                SELECT 
                    r.id, r.fecha_reserva, r.hora_reserva, s.titulo AS titulo_servicio, s.precio, s.ubicacion,
                    u_otra.nombre AS otra_persona_nombre, u_otra.apellido AS otra_persona_apellido
                FROM reservaciones r
                JOIN servicios s ON r.id_servicio = s.id 
                ";

            if ($rol === 'Cliente') {
                $sql .= " JOIN usuarios u_otra ON r.id_proveedor = u_otra.id WHERE r.id_cliente = ? AND r.estado = 'ACEPTADA'";
                $parametro = $id_usuario;
            } elseif ($rol === 'Proveedor' || $rol === 'Psicologo') {
                $sql .= " JOIN usuarios u_otra ON r.id_cliente = u_otra.id WHERE r.id_proveedor = ? AND r.estado = 'ACEPTADA'";
                $parametro = $id_usuario;
            } else {
                return [];
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$parametro]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener reservas para calendario: " . $e->getMessage());
            return [];
        }
    }
}
?>