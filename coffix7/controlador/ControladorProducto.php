<?php

require_once 'modelo/ModeloProductos.php';

class ControladorProducto {
    private $modeloProducto;

    public function __construct() {
        $this->modeloProducto = new ModeloProductos();
    }

    private function tienePermisoEmpleado() {
        if (!isset($_SESSION['usuario'])) {
            return false;
        }
        $rol = $_SESSION['usuario']['rol'] ?? '';
        return $rol === 'Empleado';
    }

    private function denegarAcceso() {
        $_SESSION['mensaje_error'] = 'Acceso denegado. Solo personal autorizado (Empleado) puede gestionar productos.';
        header('Location: index.php?ruta=inicio');
        exit();
    }

    public function mostrarFormularioAdicion() {
        if (!$this->tienePermisoEmpleado()) {
            $this->denegarAcceso();
        }

        $producto = null;
        $accion = 'agregar';
        
        require_once 'vista/plantilla/encabezado.php';
        require_once 'vista/productos/formulario_producto.php';
        require_once 'vista/plantilla/pie_pagina.php';
    }

    public function procesarAdicion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->tienePermisoEmpleado()) {
            $this->denegarAcceso();
        }
        
        $ruta_imagen = $this->manejarSubidaImagen($_FILES['imagen'] ?? null);
        
        $datos = [
            'nombre'      => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'precio'      => filter_var($_POST['precio'] ?? 0, FILTER_VALIDATE_FLOAT),
            'categoria'   => trim($_POST['categoria'] ?? ''),
            'stock'       => filter_var($_POST['stock'] ?? 0, FILTER_VALIDATE_INT),
            'imagen_ruta' => $ruta_imagen
        ];

        if ($this->modeloProducto->insertarProducto($datos)) {
            $_SESSION['mensaje_exito'] = 'Producto agregado exitosamente.';
            header('Location: index.php?ruta=inicio');
        } else {
            $_SESSION['mensaje_error'] = 'Error al agregar el producto.';
            header('Location: index.php?ruta=agregar_producto');
        }
        exit();
    }

    public function mostrarFormularioEdicion($id_producto) {
        if (!$this->tienePermisoEmpleado()) {
            $this->denegarAcceso();
        }

        $producto = $this->modeloProducto->obtenerProductoPorId($id_producto);

        if (!$producto) {
            $_SESSION['mensaje_error'] = 'Producto no encontrado.';
            header('Location: index.php?ruta=inicio');
            exit();
        }
        
        $accion = 'editar'; 
        
        require_once 'vista/plantilla/encabezado.php';
        require_once 'vista/productos/formulario_producto.php';
        require_once 'vista/plantilla/pie_pagina.php';
    }
    
    public function procesarEdicion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->tienePermisoEmpleado()) {
            $this->denegarAcceso();
        }
        
        $id_producto = $_POST['id_producto'] ?? null;
        $producto_actual = $this->modeloProducto->obtenerProductoPorId($id_producto);
        
        if (!$producto_actual) {
            $_SESSION['mensaje_error'] = 'Producto no válido para edición.';
            header('Location: index.php?ruta=inicio');
            exit();
        }

        $ruta_nueva_imagen = $this->manejarSubidaImagen($_FILES['imagen'] ?? null);
        
        $imagen_a_guardar = !empty($ruta_nueva_imagen) ? $ruta_nueva_imagen : ($producto_actual['imagen_ruta'] ?? '');

        $datos = [
            'id'          => $id_producto,
            'nombre'      => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'precio'      => filter_var($_POST['precio'] ?? 0, FILTER_VALIDATE_FLOAT),
            'categoria'   => trim($_POST['categoria'] ?? ''),
            'stock'       => filter_var($_POST['stock'] ?? 0, FILTER_VALIDATE_INT),
            'destacado'   => isset($_POST['destacado']) ? 1 : 0,
            'imagen_ruta' => $imagen_a_guardar
        ];
        
        if ($this->modeloProducto->actualizarProducto($datos)) {
            $_SESSION['mensaje_exito'] = 'Producto actualizado exitosamente.';
            header('Location: index.php?ruta=inicio');
        } else {
            $_SESSION['mensaje_error'] = 'Error al actualizar el producto.';
            header('Location: index.php?ruta=editar_producto&id=' . $id_producto);
        }
        exit();
    }

    public function procesarEliminacion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->tienePermisoEmpleado()) {
            $this->denegarAcceso();
        }
        
        $id_producto = $_POST['id_producto'] ?? null;
        
        if ($this->modeloProducto->eliminarProducto($id_producto)) {
            $_SESSION['mensaje_exito'] = 'Producto eliminado exitosamente.';
        } else {
            $_SESSION['mensaje_error'] = 'Error al intentar eliminar el producto.';
        }

        header('Location: index.php?ruta=inicio');
        exit();
    }
    
    private function manejarSubidaImagen($file_data) {
        if (!empty($file_data) && $file_data['error'] === UPLOAD_ERR_OK) {
            $directorio_destino = 'recursos/img/productos/';
            $nombre_archivo = time() . basename($file_data['name']);
            $ruta_completa = $directorio_destino . $nombre_archivo;
            
            if (!is_dir($directorio_destino)) {
                 mkdir($directorio_destino, 0777, true);
            }

            if (move_uploaded_file($file_data['tmp_name'], $ruta_completa)) {
                return $ruta_completa;
            }
        }
        return '';
    }
}
?>