<?php

require_once 'modelo/ModeloServicio.php';
require_once 'modelo/ModeloUsuario.php'; 
require_once 'modelo/ModeloReseña.php';

class ControladorServicio {
    private $modeloServicio;
    private $modeloResena; 

    public function __construct() {
        $this->modeloServicio = new ModeloServicio();
        $this->modeloResena = new ModeloReseña(); 
    }

    public function mostrarListaServicios() {
        $filtros = [
            'busqueda' => $_GET['busqueda'] ?? '',
            'ubicacion' => $_GET['ubicacion'] ?? '',
            'precio_max' => $_GET['precio_max'] ?? '',
            'calificacion_min' => $_GET['calificacion_min'] ?? '',
            'orden' => $_GET['orden'] ?? 'fecha_publicacion DESC'
        ];
        
        $servicios = $this->modeloServicio->obtenerServiciosPublicados($filtros);

        require_once 'vista/plantilla/encabezado.php';
        require_once 'vista/servicios/lista_servicios.php';
        require_once 'vista/plantilla/pie_pagina.php';
    }
    
    public function mostrarDetalleServicio($id_servicio) {
        $servicio = $this->modeloServicio->obtenerServicioPorId($id_servicio);

        if (!$servicio) {
            $_SESSION['mensaje_error'] = 'Servicio no encontrado.';
            header('Location: index.php?ruta=servicios');
            exit();
        }
        $id_proveedor = $servicio['id_usuario_servicio'] ?? $servicio['id_usuario'];
        
        $promedio_proveedor = $this->modeloServicio->obtenerPromedioCalificacionProveedor($id_proveedor);
        
        $servicio['promedio_proveedor_formato'] = number_format($promedio_proveedor, 1);
        
        $reseñas = $this->modeloResena->obtenerReseñasServicio($id_servicio); 
        
        require_once 'vista/plantilla/encabezado.php';
        require_once 'vista/servicios/detalle_servicio.php'; 
        require_once 'vista/plantilla/pie_pagina.php';
    }

    public function procesarReseñaServicio() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['usuario'])) {
            header('Location: index.php?ruta=servicios');
            exit();
        }

        $id_servicio = $_POST['id_servicio'] ?? null;
        $calificacion = (int)($_POST['calificacion'] ?? 0);
        $comentario = trim($_POST['comentario'] ?? '');
        $id_usuario = $_SESSION['usuario']['id'];

        if ($id_servicio === null || $calificacion < 1 || $calificacion > 5) {
            $_SESSION['mensaje_error'] = 'Faltan datos o la calificación es inválida.';
            header('Location: index.php?ruta=detalle_servicio&id=' . $id_servicio);
            exit();
        }
        
        $datos_reseña = [
            'id_servicio' => $id_servicio,
            'id_usuario' => $id_usuario,
            'calificacion' => $calificacion,
            'comentario' => $comentario
        ];

        $resultado = $this->modeloResena->insertarReseñaServicio($datos_reseña);

        if ($resultado) {
            $promedio_resultado = $this->modeloResena->calcularPromedioServicio($id_servicio);
            
            if ($promedio_resultado && $promedio_resultado['promedio'] !== null) {
                $this->modeloServicio->actualizarCalificacionServicio($id_servicio, $promedio_resultado['promedio']);
            }

            $_SESSION['mensaje_exito'] = '¡Gracias por tu reseña!';
        } else {
            $_SESSION['mensaje_error'] = 'Error al dejar la reseña. Puede que ya hayas calificado este servicio.';
        }

        header('Location: index.php?ruta=detalle_servicio&id=' . $id_servicio . '#reseñas');
        exit();
    }

    private function tienePermisoPublicar() {
        if (!isset($_SESSION['usuario'])) return false;
        $rol = $_SESSION['usuario']['rol'];
        return in_array($rol, ['Proveedor', 'Psicologo']);
    }

    public function mostrarFormularioPublicacion() {
        if (!$this->tienePermisoPublicar()) {
            $_SESSION['mensaje_error'] = 'Solo Proveedores y Psicologos pueden publicar servicios.';
            header('Location: index.php?ruta=perfil');
            exit();
        }

        require_once 'config/categorias.php'; 

        $rol = $_SESSION['usuario']['rol'] ?? 'Cliente'; 
        
        $categorias_agrupadas = $categorias_servicios[$rol] ?? $categorias_servicios['Proveedor'];

        require_once 'vista/plantilla/encabezado.php';
        require_once 'vista/servicios/publicar_servicio.php';
        require_once 'vista/plantilla/pie_pagina.php';
    }
    
    public function procesarPublicacion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->tienePermisoPublicar()) {
            header('Location: index.php?ruta=servicios');
            exit();
        }

        $ruta_imagen = $this->subirImagenes($_FILES); 
        
        $datos = [
            'id_usuario' => $_SESSION['usuario']['id'],
            'titulo' => $_POST['titulo'],
            'descripcion' => $_POST['descripcion'],
            'categoria' => $_POST['categoria'],
            'ubicacion' => $_POST['ubicacion'],
            'precio' => $_POST['precio'],
            'disponibilidad' => $_POST['disponibilidad'],
            'imagenes' => $ruta_imagen
        ];

        if ($this->modeloServicio->publicarServicio($datos)) {
            $_SESSION['mensaje_exito'] = 'Servicio publicado con exito!';
            header('Location: index.php?ruta=servicios');
        } else {
            $_SESSION['mensaje_error'] = 'Error al publicar el servicio.';
            header('Location: index.php?ruta=publicar_servicio');
        }
        exit();
    }
    
    public function mostrarFormularioEdicion($id_servicio) {
        $servicio = $this->modeloServicio->obtenerServicioPorId($id_servicio);

        if (!$servicio || ($servicio['id_usuario'] ?? null) != ($_SESSION['usuario']['id'] ?? null) || !$this->tienePermisoPublicar()) {
            $_SESSION['mensaje_error'] = 'No tienes permiso para editar este servicio.';
            header('Location: index.php?ruta=servicios');
            exit();
        }
        
        require_once 'config/categorias.php'; 
        $rol = $_SESSION['usuario']['rol'] ?? 'Cliente'; 
        $categorias_agrupadas = $categorias_servicios[$rol] ?? $categorias_servicios['Proveedor'];

        require_once 'vista/plantilla/encabezado.php';
        require_once 'vista/servicios/editar_servicio.php'; 
        require_once 'vista/plantilla/pie_pagina.php';
    }
    
    public function procesarEdicion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->tienePermisoPublicar()) {
            header('Location: index.php?ruta=servicios');
            exit();
        }
        
        $id_servicio = $_POST['id_servicio'] ?? null;
        $servicio_actual = $this->modeloServicio->obtenerServicioPorId($id_servicio);
        
        if (!$servicio_actual || ($servicio_actual['id_usuario'] ?? null) != ($_SESSION['usuario']['id'] ?? null)) {
            $_SESSION['mensaje_error'] = 'Error de permiso al editar.';
            header('Location: index.php?ruta=servicios');
            exit();
        }

        $ruta_nueva_imagen = $this->subirImagenes($_FILES);
        
        $imagen_a_guardar = !empty($ruta_nueva_imagen) ? $ruta_nueva_imagen : ($servicio_actual['imagenes'] ?? '');
        
        $datos = [
            'id' => $id_servicio,
            'id_usuario' => $_SESSION['usuario']['id'],
            'titulo' => $_POST['titulo'],
            'descripcion' => $_POST['descripcion'],
            'categoria' => $_POST['categoria'],
            'ubicacion' => $_POST['ubicacion'],
            'precio' => $_POST['precio'],
            'disponibilidad' => $_POST['disponibilidad'],
            'imagenes' => $imagen_a_guardar
        ];

        if ($this->modeloServicio->actualizarServicio($datos)) {
            $_SESSION['mensaje_exito'] = 'Servicio actualizado con exito.';
            header('Location: index.php?ruta=detalle_servicio&id=' . $id_servicio);
        } else {
            $_SESSION['mensaje_error'] = 'Error al actualizar el servicio.';
            header('Location: index.php?ruta=editar_servicio&id=' . $id_servicio);
        }
        exit();
    }
    
    public function procesarEliminacion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->tienePermisoPublicar()) {
            header('Location: index.php?ruta=servicios');
            exit();
        }

        $id_servicio = $_POST['id_servicio'] ?? null;
        $id_usuario = $_SESSION['usuario']['id'] ?? null;
        
        if ($this->modeloServicio->eliminarServicioPorPropietario($id_servicio, $id_usuario)) {
            $_SESSION['mensaje_exito'] = 'Servicio eliminado con exito.';
        } else {
            $_SESSION['mensaje_error'] = 'Error al eliminar el servicio. Asegúrese de que eres el propietario.';
        }
        header('Location: index.php?ruta=servicios');
        exit();
    }
    
    private function esAdministrador() {
        return isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'Administrador';
    }

    public function mostrarServiciosIngresados() {
        if (!$this->esAdministrador()) {
            header('Location: index.php');
            exit();
        }
        
        $servicios = $this->modeloServicio->obtenerTodosServiciosAdmin(); 

        require_once 'vista/plantilla/encabezado.php';
        require_once 'vista/administrador/gestion_pagina.php';
        require_once 'vista/administrador/servicios_ingresados.php';
        require_once 'vista/plantilla/pie_pagina.php';
    }
    
    public function procesarEliminacionAdmin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->esAdministrador()) {
            header('Location: index.php');
            exit();
        }

        $id_servicio = $_POST['id_servicio'] ?? null;
        $motivo = $_POST['motivo_eliminacion'] ?? '';
        $id_administrador = $_SESSION['usuario']['id'] ?? null;
        
        if (empty($motivo)) {
            $_SESSION['mensaje_error'] = 'El motivo de eliminación es obligatorio.';
            header('Location: index.php?ruta=servicios_ingresados');
            exit();
        }

        if ($this->modeloServicio->eliminarServicioPorAdmin($id_servicio, $id_administrador, $motivo)) { 
            $_SESSION['mensaje_exito'] = 'Servicio eliminado por el Administrador y registrado.';
        } else {
            $_SESSION['mensaje_error'] = 'Error al eliminar el servicio. Revise el log.';
        }
        header('Location: index.php?ruta=servicios_ingresados');
        exit();
    }
    
    private function subirImagenes($files) {
        $directorio = 'recursos/img/servicios/';
        $archivos = $files['imagenes'] ?? null; 
        
        if ($archivos === null || !isset($archivos['error'][0]) || $archivos['error'][0] !== UPLOAD_ERR_OK) {
             if ($archivos && isset($archivos['error'][0]) && $archivos['error'][0] !== UPLOAD_ERR_NO_FILE) {
                 error_log("Error de subida de PHP para imagenes: Codigo " . $archivos['error'][0]);
             }
             return "";
        }

        if (!is_dir($directorio)) {
            if (!mkdir($directorio, 0777, true)) {
                error_log("Error: No se pudo crear el directorio de subida: " . $directorio);
                return "";
            }
        }
        
        $name = $archivos['name'][0];
        $tmp_name = $archivos['tmp_name'][0];
        
        $nombre_archivo = uniqid('serv') . '-' . basename($name);
        $ruta_destino = $directorio . $nombre_archivo;
        
        if (move_uploaded_file($tmp_name, $ruta_destino)) {
            return $ruta_destino;
        } else {
            error_log("Error al mover el archivo subido: " . $tmp_name . " a " . $ruta_destino);
            return "";
        }
    }
}