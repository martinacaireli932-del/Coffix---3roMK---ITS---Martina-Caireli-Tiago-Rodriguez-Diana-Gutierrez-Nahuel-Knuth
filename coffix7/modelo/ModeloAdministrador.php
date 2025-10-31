<?php

class ModeloAdministrador {
    private $conexion;

    public function __construct() {
        $nombre_bd = "coffix_bd"; 
        $usuario_bd = "root"; 
        $contrasena_bd = ""; 
        
        try {
            $this->conexion = new PDO("mysql:host=localhost;dbname=$nombre_bd", $usuario_bd, $contrasena_bd);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            die("Error de conexion: " . $e->getMessage());
        }
    }
    
    public function obtenerTodosUsuariosConEstado() {
        $usuarios_activos = "
            SELECT 
                id, nombre, apellido, nombre_usuario, correo, rol, fecha_registro, 
                'Activo' AS estado, NULL AS motivo_bloqueo, NULL AS fecha_bloqueo 
            FROM 
                usuarios
        ";
        
        $usuarios_bloqueados = "
            SELECT 
                id, nombre, apellido, nombre_usuario, correo, rol, fecha_registro, 
                'Bloqueado' AS estado, motivo_bloqueo, fecha_bloqueo 
            FROM 
                usuarios_bloqueados
        ";

        $query = "({$usuarios_activos}) UNION ALL ({$usuarios_bloqueados}) ORDER BY fecha_registro DESC";

        try {
            $stmt = $this->conexion->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener usuarios y bloqueados: " . $e->getMessage());
            die("Error SQL al obtener usuarios: " . $e->getMessage() . "<br>Verifique que todas las columnas existan en ambas tablas.");
            return [];
        }
    }

    public function bloquearUsuario($id_usuario, $motivo, $fecha) {
        try {
            $this->conexion->beginTransaction();

            $query_select = "SELECT * FROM usuarios WHERE id = :id";
            $stmt_select = $this->conexion->prepare($query_select);
            $stmt_select->execute(['id' => $id_usuario]);
            $usuario = $stmt_select->fetch();

            if (!$usuario) {
                $this->conexion->rollBack();
                return false;
            }
            
            $query_insert = "
                INSERT INTO usuarios_bloqueados (
                    id, nombre, apellido, nombre_usuario, contrasena, fecha_nacimiento, sexo, 
                    correo, numero_contacto, rol, biografia, foto_perfil, fecha_registro,
                    motivo_bloqueo, fecha_bloqueo
                ) VALUES (
                    :id, :nombre, :apellido, :nombre_usuario, :contrasena, :fecha_nacimiento, :sexo, 
                    :correo, :numero_contacto, :rol, :biografia, :foto_perfil, :fecha_registro,
                    :motivo_bloqueo, :fecha_bloqueo
                )
            ";
            $stmt_insert = $this->conexion->prepare($query_insert);
            $success = $stmt_insert->execute(array_merge($usuario, [
                'motivo_bloqueo' => $motivo,
                'fecha_bloqueo' => $fecha
            ]));
            
            if (!$success) {
                $this->conexion->rollBack();
                return false;
            }

            $query_delete = "DELETE FROM usuarios WHERE id = :id";
            $stmt_delete = $this->conexion->prepare($query_delete);
            $success = $stmt_delete->execute(['id' => $id_usuario]);
            
            if (!$success) {
                $this->conexion->rollBack();
                return false;
            }

            $this->conexion->commit();
            return true;

        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log("Error al bloquear usuario: " . $e->getMessage());
            return false;
        }
    }

    public function desbloquearUsuario($id_usuario) {
        try {
            $this->conexion->beginTransaction();

            $query_select = "SELECT * FROM usuarios_bloqueados WHERE id = :id";
            $stmt_select = $this->conexion->prepare($query_select);
            $stmt_select->execute(['id' => $id_usuario]);
            $usuario_bloqueado = $stmt_select->fetch();

            if (!$usuario_bloqueado) {
                $this->conexion->rollBack();
                return false;
            }

            $query_insert = "
                INSERT INTO usuarios (
                    id, nombre, apellido, nombre_usuario, contrasena, fecha_nacimiento, sexo, 
                    correo, numero_contacto, rol, biografia, foto_perfil, fecha_registro
                ) VALUES (
                    :id, :nombre, :apellido, :nombre_usuario, :contrasena, :fecha_nacimiento, :sexo, 
                    :correo, :numero_contacto, :rol, :biografia, :foto_perfil, :fecha_registro
                )
            ";
            $stmt_insert = $this->conexion->prepare($query_insert);
            
            $campos_extra = ['motivo_bloqueo', 'fecha_bloqueo']; 
            $datos_a_insertar = array_diff_key($usuario_bloqueado, array_flip($campos_extra));
            
            $success = $stmt_insert->execute($datos_a_insertar);
            
            if (!$success) {
                $this->conexion->rollBack();
                return false;
            }

            $query_delete = "DELETE FROM usuarios_bloqueados WHERE id = :id";
            $stmt_delete = $this->conexion->prepare($query_delete);
            $success = $stmt_delete->execute(['id' => $id_usuario]);

            if (!$success) {
                $this->conexion->rollBack();
                return false;
            }

            $this->conexion->commit();
            return true;

        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                 $this->conexion->rollBack();
                 $query_delete = "DELETE FROM usuarios_bloqueados WHERE id = :id";
                 $stmt_delete = $this->conexion->prepare($query_delete);
                 $stmt_delete->execute(['id' => $id_usuario]);
                 error_log("Error de desbloqueo (Clave Duplicada): El usuario ID $id_usuario ya estaba activo.");
                 return true;
            }
            $this->conexion->rollBack();
            error_log("Error al desbloquear usuario: " . $e->getMessage());
            return false;
        }
    }
}