<?php

require_once 'modelo/ModeloReseña.php';
require_once 'modelo/ModeloServicio.php'; 

class ControladorReseña {
    private $modeloReseña;
    private $modeloServicio; 

    public function __construct() {
        $this->modeloReseña = new ModeloReseña();
        $this->modeloServicio = new ModeloServicio(); 
    }

    public function procesarReseñaServicio() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['usuario'])) {
            header('Location: index.php?ruta=servicios');
            exit();
        }

        $id_servicio = filter_input(INPUT_POST, 'id_servicio', FILTER_VALIDATE_INT);
        $calificacion = filter_input(INPUT_POST, 'calificacion', FILTER_VALIDATE_INT);
        $comentario = filter_input(INPUT_POST, 'comentario', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $id_usuario = $_SESSION['usuario']['id'];

        if (!$id_servicio || $calificacion === false || $calificacion === null || $calificacion < 1 || $calificacion > 5) {
            $_SESSION['mensaje_error'] = 'Datos de reseña incompletos o inválidos.';
            $redirect_id = $id_servicio ?: 'servicios'; 
            header('Location: index.php?ruta=detalle_servicio&id=' . $redirect_id);
            exit();
        }

        $datos = [
            'id_servicio' => $id_servicio,
            'id_usuario' => $id_usuario,
            'calificacion' => $calificacion,
            'comentario' => $comentario
        ];

        if ($this->modeloReseña->insertarReseñaServicio($datos)) {
            
            $resultado_promedio = $this->modeloReseña->calcularPromedioServicio($id_servicio);
            
            if ($resultado_promedio && isset($resultado_promedio['promedio'])) {
                $promedio_float = (float)$resultado_promedio['promedio'];
                $this->modeloServicio->actualizarCalificacionServicio($id_servicio, $promedio_float); 
            }
            
            $_SESSION['mensaje_exito'] = '¡Gracias por tu reseña!';
            
        } else {
            $_SESSION['mensaje_error'] = 'Error al dejar la reseña. Ya calificaste este servicio o hubo un error.'; 
        }

        header('Location: index.php?ruta=detalle_servicio&id=' . $id_servicio . '#reseñas');
        exit();
    }
    
    public function mostrarComentariosWeb() {
        $reseñas = $this->modeloReseña->obtenerReseñasWeb();
        
        require_once 'vista/plantilla/encabezado.php';
        require_once 'vista/reseñas/comentarios_web.php';
        require_once 'vista/plantilla/pie_pagina.php';
    }

    public function procesarReseñaWeb() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['usuario'])) {
            header('Location: index.php?ruta=comentarios_web');
            exit();
        }

        $calificacion = filter_input(INPUT_POST, 'calificacion', FILTER_VALIDATE_INT);
        $comentario_limpio = trim($_POST['comentario'] ?? '');
        $id_usuario = $_SESSION['usuario']['id'];

        if ($calificacion === null || $calificacion === false || $calificacion < 1 || $calificacion > 5 || empty($comentario_limpio)) {
            $_SESSION['mensaje_error'] = 'La calificación (1-5) y el comentario son obligatorios.';
            header('Location: index.php?ruta=comentarios_web');
            exit();
        }

        $datos = [
            'id_usuario' => $id_usuario,
            'calificacion' => $calificacion,
            'comentario' => $comentario_limpio
        ];

        if ($this->modeloReseña->insertarReseñaWeb($datos)) {
            $_SESSION['mensaje_exito'] = '¡Gracias por tu reseña sobre nuestro sitio web!';
        } else {
            $_SESSION['mensaje_error'] = 'Error al dejar la reseña del sitio. Podrías haber dejado una ya.'; 
        }

        header('Location: index.php?ruta=comentarios_web');
        exit();
    }
}