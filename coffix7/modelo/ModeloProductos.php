<?php
require_once 'conexionBD.php';

class ModeloProductos {
    private $pdo; 

    public function __construct() {
        $this->pdo = ModeloConexion::obtenerInstancia()->obtenerPDO();
    }

    public function obtenerProductoPorId($id) {
        $sql = "SELECT * FROM productos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function insertarProducto($datos) {
        $sql = "INSERT INTO productos (nombre, descripcion, precio, categoria, imagen_ruta, stock, destacado, ventas_acumuladas) 
                 VALUES (:nombre, :descripcion, :precio, :categoria, :imagen_ruta, :stock, 0, 0)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':precio', $datos['precio']);
            $stmt->bindParam(':categoria', $datos['categoria']);
            $stmt->bindParam(':imagen_ruta', $datos['imagen_ruta']); 
            $stmt->bindParam(':stock', $datos['stock'], PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al insertar producto: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarProducto($datos) {
        $sql = "UPDATE productos SET 
                    nombre = :nombre, descripcion = :descripcion, precio = :precio, 
                    categoria = :categoria, imagen_ruta = :imagen_ruta, stock = :stock, 
                    destacado = :destacado
                WHERE id = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':precio', $datos['precio']);
            $stmt->bindParam(':categoria', $datos['categoria']);
            $stmt->bindParam(':imagen_ruta', $datos['imagen_ruta']); 
            $stmt->bindParam(':stock', $datos['stock'], PDO::PARAM_INT);
            $stmt->bindParam(':destacado', $datos['destacado'], PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar producto: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarProducto($id) {
        $sql = "DELETE FROM productos WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar producto: " . $e->getMessage());
            return false;
        }
    }
    
    public function obtenerProductosDestacados($limite = 6) {
        $sql = "SELECT * FROM productos WHERE destacado = 1 LIMIT :limite";
        $stmt = $this->pdo->prepare($sql); 
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProductosMasVendidos($limite = 6) {
        $sql = "SELECT * FROM productos ORDER BY ventas_acumuladas DESC LIMIT :limite";
        
        try {
            $stmt = $this->pdo->prepare($sql); 
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener productos más vendidos: " . $e->getMessage());
            return [];
        }
    }
}
?>