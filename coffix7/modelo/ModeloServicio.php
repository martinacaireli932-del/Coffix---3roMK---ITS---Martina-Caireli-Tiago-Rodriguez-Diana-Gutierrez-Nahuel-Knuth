<?php

require_once __DIR__ . '/conexionBD.php';

class ModeloServicio {
    private $pdo;

    public function __construct() {
        $conexion = ModeloConexion::obtenerInstancia(); 
        $this->pdo = $conexion->obtenerPDO(); 
    }
    
    /** *
     * * @param int
     * @return float
     */
    public function obtenerPromedioCalificacionProveedor($id_usuario) {
        $sql = "
            SELECT 
                AVG(COALESCE(calificacion_promedio, 0)) AS promedio_proveedor
            FROM servicios
            WHERE id_usuario = :id_usuario
        ";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return (float) ($resultado['promedio_proveedor'] ?? 0.0);

        } catch (PDOException $e) {
            error_log("Error al calcular el promedio del proveedor: " . $e->getMessage());
            return 0.0;
        }
    }

    public function obtenerServiciosPorUsuario($id_usuario) {
        $sql = "
            SELECT 
                s.*, 
                u.nombre AS nombre_usuario 
            FROM servicios s
            LEFT JOIN usuarios u ON s.id_usuario = u.id
            WHERE s.id_usuario = :id_usuario
            ORDER BY s.fecha_publicacion DESC
        ";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener servicios por usuario: " . $e->getMessage());
            return [];
        }
    }

    public function actualizarCalificacionServicio($id_servicio, $promedio) {
        $promedio_redondeado = round($promedio, 2); 
        $sql = "UPDATE servicios SET calificacion_promedio = :promedio WHERE id = :id_servicio";
        
        try {
            $stmt = $this->pdo->prepare($sql); 
            $stmt->bindParam(':promedio', $promedio_redondeado);
            $stmt->bindParam(':id_servicio', $id_servicio, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar calificación de servicio: " . $e->getMessage());
            return false;
        }
    }
    
    public function obtenerServiciosPublicados(array $filtros) {
        $sql = "
            SELECT 
                s.*, 
                s.calificacion_promedio AS calificacion, 
                u.nombre AS nombre_usuario, 
                u.foto_perfil 
            FROM servicios s
            LEFT JOIN usuarios u ON s.id_usuario = u.id
            WHERE s.estado = 'Activo' 
        ";
        
        $params = [];
        
        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (s.titulo LIKE :busqueda OR s.descripcion LIKE :busqueda)";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }

        if (!empty($filtros['ubicacion'])) {
            $sql .= " AND s.ubicacion LIKE :ubicacion";
            $params[':ubicacion'] = '%' . $filtros['ubicacion'] . '%';
        }

        if (!empty($filtros['precio_max']) && is_numeric($filtros['precio_max'])) {
            $sql .= " AND s.precio <= :precio_max";
            $params[':precio_max'] = $filtros['precio_max'];
        }

        if (!empty($filtros['calificacion_min']) && is_numeric($filtros['calificacion_min'])) {
            $sql .= " AND s.calificacion_promedio >= :calificacion_min";
            $params[':calificacion_min'] = $filtros['calificacion_min'];
        }

        $orden = $filtros['orden'] ?? 'fecha_publicacion DESC';
        $sql .= " ORDER BY " . $orden;
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params); 
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener servicios publicados: " . $e->getMessage());
            return [];
        }
    }
    
    public function obtenerServicioPorId($id_servicio) {
        $sql = "SELECT s.*, u.foto_perfil, u.nombre AS nombre_usuario, u.rol, u.id AS id_usuario_servicio 
                FROM servicios s 
                LEFT JOIN usuarios u ON s.id_usuario = u.id 
                WHERE s.id = :id_servicio";
                
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_servicio', $id_servicio, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener servicio por ID: " . $e->getMessage());
            return false;
        }
    }
    
    public function publicarServicio($datos) {
        $sql = "INSERT INTO servicios (id_usuario, titulo, descripcion, categoria, ubicacion, precio, disponibilidad, imagenes, estado, calificacion_promedio) 
                VALUES (:id_usuario, :titulo, :descripcion, :categoria, :ubicacion, :precio, :disponibilidad, :imagenes, 'Activo', NULL)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
            $stmt->bindParam(':titulo', $datos['titulo']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':categoria', $datos['categoria']);
            $stmt->bindParam(':ubicacion', $datos['ubicacion']);
            $stmt->bindParam(':precio', $datos['precio']);
            $stmt->bindParam(':disponibilidad', $datos['disponibilidad']);
            $stmt->bindParam(':imagenes', $datos['imagenes']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al publicar servicio: " . $e->getMessage());
            return false;
        }
    }
    
    public function actualizarServicio($datos) {
        $sql = "UPDATE servicios SET 
                    titulo = :titulo, descripcion = :descripcion, categoria = :categoria, 
                    ubicacion = :ubicacion, precio = :precio, disponibilidad = :disponibilidad, 
                    imagenes = :imagenes 
                WHERE id = :id AND id_usuario = :id_usuario";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);
            $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
            $stmt->bindParam(':titulo', $datos['titulo']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':categoria', $datos['categoria']);
            $stmt->bindParam(':ubicacion', $datos['ubicacion']);
            $stmt->bindParam(':precio', $datos['precio']);
            $stmt->bindParam(':disponibilidad', $datos['disponibilidad']);
            $stmt->bindParam(':imagenes', $datos['imagenes']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar servicio: " . $e->getMessage());
            return false;
        }
    }
    
    public function eliminarServicioPorPropietario($id_servicio, $id_usuario) {
        $sql = "DELETE FROM servicios WHERE id = :id_servicio AND id_usuario = :id_usuario";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_servicio', $id_servicio, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar servicio por propietario: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerTodosServiciosAdmin() {
        $sql = "SELECT * FROM servicios ORDER BY fecha_publicacion DESC";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener todos los servicios (Admin): " . $e->getMessage());
            return [];
        }
    }
    
    public function eliminarServicioPorAdmin($id_servicio, $id_administrador, $motivo) {
        $sql = "DELETE FROM servicios WHERE id = :id_servicio"; 
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_servicio', $id_servicio, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Error al eliminar servicio por administrador: " . $e->getMessage());
            return false;
        }
    }
}