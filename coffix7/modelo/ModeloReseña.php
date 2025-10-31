<?php

require_once __DIR__ . '/conexionBD.php';

class ModeloReseña {
    private $pdo;

    public function __construct() {
        $conexion = ModeloConexion::obtenerInstancia();
        $this->pdo = $conexion->obtenerPDO(); 
    }

    public function insertarReseñaServicio($datos) {
        $sql = "INSERT INTO reseñas_servicios (id_servicio, id_usuario, calificacion, comentario) 
                VALUES (:id_servicio, :id_usuario, :calificacion, :comentario)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_servicio', $datos['id_servicio'], PDO::PARAM_INT);
            $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
            $stmt->bindParam(':calificacion', $datos['calificacion'], PDO::PARAM_INT);
            $stmt->bindParam(':comentario', $datos['comentario']);
            
            $exito = $stmt->execute();

            if (!$exito) {
                error_log("Fallo en execute() para insertarReseñaServicio. Info: " . print_r($stmt->errorInfo(), true));
                return false; 
            }
            
            return $exito;

        } catch (PDOException $e) {
            $errorCode = $e->getCode();
            
            if ($errorCode == '23000') {
                if (strpos($e->getMessage(), 'foreign key constraint fails') !== false || strpos($e->getMessage(), 'Foreign key') !== false) {
                    die("🛑 DIAGNÓSTICO FATAL BD: El servicio o usuario ID no existe (Clave Foránea Fallida). Verifique sus claves. Error: " . $e->getMessage()); 
                }

                error_log("Error: Usuario ya dejó una reseña para este servicio (Error 23000 - Unique Key).");
                return false; 
            }
            
            die("🛑 DIAGNÓSTICO FATAL BD: Error desconocido de PDO. Código: " . $errorCode . " Mensaje: " . $e->getMessage());
        }
    }

    public function calcularPromedioServicio($id_servicio) {
        $sql = "SELECT AVG(calificacion) as promedio FROM reseñas_servicios WHERE id_servicio = :id_servicio";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_servicio', $id_servicio, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al calcular promedio: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerReseñasServicio($id_servicio) {
        $sql = "SELECT r.*, u.nombre, u.apellido FROM reseñas_servicios r 
                JOIN usuarios u ON r.id_usuario = u.id 
                WHERE r.id_servicio = :id_servicio 
                ORDER BY r.fecha_reseña DESC";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_servicio', $id_servicio, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener reseñas de servicio: " . $e->getMessage());
            return [];
        }
    }
    
    public function insertarReseñaWeb($datos) {
        $sql = "INSERT INTO reseñas_web (id_usuario, calificacion, comentario) 
                VALUES (:id_usuario, :calificacion, :comentario)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
            $stmt->bindParam(':calificacion', $datos['calificacion'], PDO::PARAM_INT);
            $stmt->bindParam(':comentario', $datos['comentario']);
            return $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                error_log("Error: Usuario ya dejó una reseña web (Unique Key).");
            } else {
                error_log("Error al insertar reseña web: " . $e->getMessage());
            }
            return false;
        }
    }

    public function obtenerReseñasWeb() {
        $sql = "
            SELECT 
                r.*, 
                u.nombre, 
                u.apellido, 
                u.nombre_usuario, 
                u.foto_perfil 
            FROM reseñas_web r
            JOIN usuarios u ON r.id_usuario = u.id 
            ORDER BY r.fecha_reseña DESC
        ";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener reseñas web: " . $e->getMessage());
            return [];
        }
    }
}