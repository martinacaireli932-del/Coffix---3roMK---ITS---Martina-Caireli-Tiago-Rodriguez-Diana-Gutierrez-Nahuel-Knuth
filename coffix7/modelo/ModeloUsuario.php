<?php

class ModeloUsuario {
    private $conexion; 

    public function __construct() {
        $nombre_bd = "coffix_bd";
        $usuario_bd = "root";
        $contrasena_bd = "";
        
        try {
            $this->conexion = new PDO("mysql:host=localhost;dbname=$nombre_bd", $usuario_bd, $contrasena_bd);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch (PDOException $e) {
            die("Error de conexion: " . $e->getMessage());
        }
    }
    
    public function verificarBloqueo($usuario_o_correo) {
        $query = "SELECT motivo_bloqueo, fecha_bloqueo FROM usuarios_bloqueados WHERE nombre_usuario = :login OR correo = :login";
        try {
            $stmt = $this->conexion->prepare($query);
            $stmt->execute(['login' => $usuario_o_correo]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al verificar bloqueo: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerUsuarioPorNombreOCorreo($usuario_o_correo) {
        $query = "SELECT * FROM usuarios WHERE nombre_usuario = :login OR correo = :login";
        try {
            $stmt = $this->conexion->prepare($query);
            $stmt->execute(['login' => $usuario_o_correo]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuario para login: " . $e->getMessage());
            return false;
        }
    }

    public function registrarUsuario($datos) {
        $query = "INSERT INTO usuarios (nombre, apellido, nombre_usuario, contrasena, fecha_nacimiento, sexo, correo, numero_contacto, rol, biografia, foto_perfil) 
                      VALUES (:nombre, :apellido, :nombre_usuario, :contrasena, :fecha_nacimiento, :sexo, :correo, :numero_contacto, :rol, :biografia, :foto_perfil)";
        try {
            $stmt = $this->conexion->prepare($query);
            
            return $stmt->execute($datos);
        } catch (PDOException $e) {
            error_log("Error de registro: " . $e->getMessage());
            return false;
        }
    }

    public function verificarCredenciales($usuario_o_correo, $contrasena) {
        return false;
    }

    public function actualizarUsuario($datos) {
        if (!isset($datos['id'])) {
            error_log("No se proporciono ID de usuario para actualizar.");
            return false;
        }

        $campos_actualizar = [];
        $parametros = ['id' => $datos['id']]; 
        
        $campos_permitidos = ['nombre', 'apellido', 'nombre_usuario', 'contrasena', 'fecha_nacimiento', 'sexo', 'correo', 'numero_contacto', 'biografia', 'foto_perfil'];

        foreach ($campos_permitidos as $campo) {
            if (array_key_exists($campo, $datos)) {
                $campos_actualizar[] = "`$campo` = :$campo";
                $parametros[$campo] = $datos[$campo];
            }
        }

        if (empty($campos_actualizar)) {
            return true;
        }

        $query = "UPDATE usuarios SET " . implode(', ', $campos_actualizar) . " WHERE id = :id";
        
        try {
            $stmt = $this->conexion->prepare($query);
            return $stmt->execute($parametros);
        } catch (PDOException $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerUsuarioPorId($id) {
        $query = "SELECT * FROM usuarios WHERE id = :id";
        try {
            $stmt = $this->conexion->prepare($query);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuario: " . $e->getMessage());
            return false;
        }
    }
    
    public function eliminarCuenta($id) {
        $query = "DELETE FROM usuarios WHERE id = :id";
        try {
            $stmt = $this->conexion->prepare($query);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar cuenta: " . $e->getMessage());
            return false;
        }
    }
}
?>