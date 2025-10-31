<?php

require_once 'modelo/ModeloAdministrador.php';
require_once 'modelo/ModeloServicio.php'; 

class ControladorAdministrador {
    private $modeloAdmin;
    private $modeloServicio;

    public function __construct() {
        $this->modeloAdmin = new ModeloAdministrador();
        $this->modeloServicio = new ModeloServicio();
    }

    private function verificarAdmin() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Administrador') {
            $_SESSION['mensaje_error'] = 'Acceso denegado. Solo para Administradores.';
            header('Location: index.php?ruta=iniciar_sesion');
            exit();
        }
    }

    private function cargarVistaAdmin($vista_contenido, $datos = []) {
        $this->verificarAdmin();
        extract($datos);
        
        require_once 'vista/plantilla/encabezado.php';
        require_once 'vista/administrador/gestion_pagina.php';
        require_once $vista_contenido;
        require_once 'vista/plantilla/pie_pagina.php';
    }

    public function mostrarServiciosIngresados() {
        $servicios = $this->modeloServicio->obtenerTodosServiciosAdmin();
        $this->cargarVistaAdmin('vista/administrador/servicios_ingresados.php', ['servicios' => $servicios]);
    }
    
    public function mostrarUsuariosBloqueos() {
        $usuarios = $this->modeloAdmin->obtenerTodosUsuariosConEstado();
        $this->cargarVistaAdmin('vista/administrador/usuarios_bloqueos.php', ['usuarios' => $usuarios]);
    }

    public function procesarEliminacionServicio() {
        $this->verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_servicio'], $_POST['motivo_eliminacion'])) {
            header('Location: index.php?ruta=servicios_ingresados');
            exit();
        }
        
        $id_servicio = (int)$_POST['id_servicio'];
        $motivo = trim($_POST['motivo_eliminacion']);
        $id_administrador = $_SESSION['usuario']['id'];

        if (empty($motivo)) {
            $_SESSION['mensaje_error'] = 'El motivo de la eliminación es obligatorio.';
            header('Location: index.php?ruta=servicios_ingresados');
            exit();
        }
        
        if ($this->modeloServicio->eliminarServicioPorAdmin($id_servicio, $id_administrador, $motivo)) {
            $_SESSION['mensaje_exito'] = "Servicio ID $id_servicio marcado como eliminado.";
        } else {
            $_SESSION['mensaje_error'] = "Error al eliminar el Servicio ID $id_servicio.";
        }
        
        header('Location: index.php?ruta=servicios_ingresados');
        exit();
    }
    
    public function procesarBloqueoUsuario() {
        $this->verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_usuario'], $_POST['motivo_bloqueo'])) {
            header('Location: index.php?ruta=usuarios_bloqueos');
            exit();
        }

        $id_usuario = $_POST['id_usuario'];
        $motivo = trim($_POST['motivo_bloqueo']);
        $fecha = date('Y-m-d H:i:s');

        if (empty($motivo)) {
            $_SESSION['mensaje_error'] = 'El motivo de bloqueo es obligatorio.';
            header('Location: index.php?ruta=usuarios_bloqueos');
            exit();
        }

        if ($this->modeloAdmin->bloquearUsuario($id_usuario, $motivo, $fecha)) {
            $_SESSION['mensaje_exito'] = "Usuario ID $id_usuario bloqueado con éxito.";
        } else {
            $_SESSION['mensaje_error'] = "Error al bloquear al Usuario ID $id_usuario. Puede que ya estuviera bloqueado.";
        }
        
        header('Location: index.php?ruta=usuarios_bloqueos');
        exit();
    }
    
    public function procesarDesbloqueoUsuario() {
        $this->verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_usuario'])) {
            header('Location: index.php?ruta=usuarios_bloqueos');
            exit();
        }

        $id_usuario = $_POST['id_usuario'];

        if ($this->modeloAdmin->desbloquearUsuario($id_usuario)) {
            $_SESSION['mensaje_exito'] = "Usuario ID $id_usuario desbloqueado y restaurado con éxito.";
        } else {
            $_SESSION['mensaje_error'] = "Error al desbloquear al Usuario ID $id_usuario. Puede que ya estuviera activo.";
        }
        
        header('Location: index.php?ruta=usuarios_bloqueos');
        exit();
    }
}