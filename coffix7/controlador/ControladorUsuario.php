<?php

require_once 'modelo/ModeloUsuario.php'; 
require_once 'modelo/ModeloServicio.php'; 

class ControladorUsuario {
    private $modeloUsuario;

    public function __construct() {
        $this->modeloUsuario = new ModeloUsuario();
    }
    
    public function mostrarPerfil() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?ruta=iniciar_sesion');
            exit;
        }

        $id_usuario = $_SESSION['usuario']['id'];
        
        $usuario = $this->modeloUsuario->obtenerUsuarioPorId($id_usuario); 

        if (!$usuario) {
            session_unset();
            session_destroy();
            header('Location: index.php?ruta=iniciar_sesion');
            exit;
        }

        require_once 'vista/plantilla/encabezado.php';
        require_once 'vista/usuario/perfil.php'; 
        require_once 'vista/plantilla/pie_pagina.php';
    }

    public function procesarEdicionPerfil() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['usuario'])) {
            header('Location: index.php?ruta=perfil');
            exit;
        }

        $usuario_actual = $this->modeloUsuario->obtenerUsuarioPorId($_SESSION['usuario']['id']);
        $ruta_foto_actual = $usuario_actual['foto_perfil'] ?? 'recursos/img/perfiles/default.png';

        $nueva_foto_ruta = $this->subirFotoPerfil($_FILES); 

        $datos_actualizar['foto_perfil'] = $ruta_foto_actual;

        if (!empty($nueva_foto_ruta)) {
            $datos_actualizar['foto_perfil'] = $nueva_foto_ruta;
            
            if ($ruta_foto_actual !== 'recursos/img/perfiles/default.png' && file_exists($ruta_foto_actual)) {
                @unlink($ruta_foto_actual); 
            }
        } 
        
        $datos_actualizar['id'] = $_SESSION['usuario']['id'];
        $datos_actualizar['nombre'] = $_POST['nombre'] ?? '';
        $datos_actualizar['apellido'] = $_POST['apellido'] ?? '';
        $datos_actualizar['nombre_usuario'] = $_POST['nombre_usuario'] ?? '';
        $datos_actualizar['fecha_nacimiento'] = $_POST['fecha_nacimiento'] ?? null;
        $datos_actualizar['sexo'] = $_POST['sexo'] ?? '';
        $datos_actualizar['correo'] = $_POST['correo'] ?? '';
        $datos_actualizar['biografia'] = $_POST['biografia'] ?? null;
        $numero_contacto = $_POST['numero_contacto'] ?? null;

        
        if (isset($_POST['contrasena']) && !empty($_POST['contrasena'])) {
            $contrasena_nueva = $_POST['contrasena'];
            $contrasena_confirmar = $_POST['contrasena_confirmar'] ?? ''; 
            $errores_pwd = [];
            
            if ($contrasena_nueva !== $contrasena_confirmar) {
                $errores_pwd[] = "Las contraseñas no coinciden.";
            }

            if (!empty($errores_pwd)) {
                $_SESSION['mensaje_error'] = 'Error al cambiar contraseña: ' . implode('<br>', $errores_pwd);
                header('Location: index.php?ruta=perfil');
                exit;
            }
            
            $datos_actualizar['contrasena'] = password_hash($contrasena_nueva, PASSWORD_DEFAULT);
        }

        $regex_contacto = '/^09\d{7}$/'; 
        if (!empty($numero_contacto) && !preg_match($regex_contacto, $numero_contacto)) {
            $_SESSION['mensaje_error'] = "El número de contacto debe ser de 9 dígitos y comenzar con '09'.";
            header('Location: index.php?ruta=perfil');
            exit;
        }
        $datos_actualizar['numero_contacto'] = $numero_contacto;
        

        if ($this->modeloUsuario->actualizarUsuario($datos_actualizar)) {
            $_SESSION['usuario'] = $this->modeloUsuario->obtenerUsuarioPorId($_SESSION['usuario']['id']); 
            $_SESSION['mensaje_exito'] = 'Perfil actualizado con exito.';
        } else {
            $_SESSION['mensaje_error'] = 'Error al actualizar el perfil. Revise los datos. El nombre de usuario/correo podría estar en uso.';
        }
        
        header('Location: index.php?ruta=perfil');
        exit;
    }

    private function subirFotoPerfil($files) {
        $directorio = 'recursos/img/perfiles/';
        
        if (!isset($files['foto_perfil']) || $files['foto_perfil']['error'] === UPLOAD_ERR_NO_FILE) {
            return "";
        }
        
        $archivo = $files['foto_perfil'];

        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            error_log("Error de subida de perfil: Codigo " . $archivo['error']);
            return "";
        }
        
        $tipo_mime = mime_content_type($archivo['tmp_name']);
        if (!in_array($tipo_mime, ['image/jpeg', 'image/png', 'image/gif'])) {
            error_log("Error de subida: Tipo de archivo no permitido: " . $tipo_mime);
            return "";
        }
        
        if (!is_dir($directorio)) {
            if (!mkdir($directorio, 0777, true)) {
                error_log("Error: No se pudo crear el directorio de perfiles.");
                return "";
            }
        }
        
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombre_archivo = uniqid('user') . '.' . $extension;
        $ruta_destino = $directorio . $nombre_archivo;
        
        if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
            return $ruta_destino;
        } else {
            error_log("Error al mover el archivo de perfil subido.");
            return "";
        }
    }
}