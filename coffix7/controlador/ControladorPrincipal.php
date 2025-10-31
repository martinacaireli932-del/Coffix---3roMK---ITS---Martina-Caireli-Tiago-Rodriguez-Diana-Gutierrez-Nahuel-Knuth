<?php

require_once 'modelo/ModeloProductos.php';
require_once 'controlador/ControladorUsuario.php'; 
require_once 'controlador/ControladorServicio.php';
require_once 'controlador/ControladorReseña.php';

require_once 'controlador/ControladorMensajeria.php'; 
require_once 'controlador/ControladorReserva.php';
require_once 'controlador/ControladorCalendario.php'; 

require_once 'controlador/ControladorAdministrador.php'; 

require_once 'controlador/ControladorProducto.php';


class ControladorPrincipal {
    private $modeloProductos;
    private $controladorUsuario; 
    private $controladorServicio;
    private $controladorResena;
    private $controladorMensajeria; 
    private $controladorReserva;
    private $controladorCalendario;
    private $controladorAdmin; 
    private $controladorProducto;

    public function __construct() {
        $this->modeloProductos = new ModeloProductos();
        $this->controladorUsuario = new ControladorUsuario(); 
        $this->controladorServicio = new ControladorServicio();
        $this->controladorResena = new ControladorReseña(); 
        $this->controladorMensajeria = new ControladorMensajeria(); 
        $this->controladorReserva = new ControladorReserva(); 
        $this->controladorCalendario = new ControladorCalendario(); 
        $this->controladorAdmin = new ControladorAdministrador(); 
        $this->controladorProducto = new ControladorProducto();
    }

    public function manejarPeticion() {
        $ruta = $_GET['ruta'] ?? 'inicio';
        
        if ($ruta === 'agregar_producto') {
            $this->controladorProducto->mostrarFormularioAdicion();
            return;
        } elseif ($ruta === 'editar_producto' && isset($_GET['id'])) {
            $this->controladorProducto->mostrarFormularioEdicion((int)$_GET['id']);
            return;
        }
        
        if ($ruta === 'servicios') {
            $this->controladorServicio->mostrarListaServicios();
            return;
        } elseif ($ruta === 'detalle_servicio' && isset($_GET['id'])) {
            $this->controladorServicio->mostrarDetalleServicio((int)$_GET['id']);
            return;
        } elseif ($ruta === 'publicar_servicio') {
            $this->controladorServicio->mostrarFormularioPublicacion();
            return;
        } elseif ($ruta === 'editar_servicio' && isset($_GET['id'])) {
            $this->controladorServicio->mostrarFormularioEdicion((int)$_GET['id']);
            return;
        } 
        
        if ($ruta === 'comentarios_web') {
            $this->controladorResena->mostrarComentariosWeb();
            return;
        }

        if ($ruta === 'gestion_pagina' || $ruta === 'servicios_ingresados') {
            $this->controladorAdmin->mostrarServiciosIngresados();
            return;
        } 
        elseif ($ruta === 'usuarios_bloqueos') {
            $this->controladorAdmin->mostrarUsuariosBloqueos();
            return;
        }

        if ($ruta === 'registro') {
            $this->controladorUsuario->mostrarRegistro();
            return;
        } elseif ($ruta === 'iniciar_sesion') {
            $this->controladorUsuario->mostrarLogin();
            return;
        } elseif ($ruta === 'perfil') {
            $this->controladorUsuario->mostrarPerfil(); 
            return;
        } 
        
        if ($ruta === 'cerrar_sesion') {
            $this->controladorUsuario->procesarCerrarSesion();
            return;
        }

        if ($ruta === 'calendario') {
            $this->controladorCalendario->cargarVistaCalendario();
            return;
        } elseif ($ruta === 'calendario/eventos') {
            $this->controladorCalendario->obtenerEventosCalendario();
            return;
        }

        if ($ruta === 'notificaciones') {
            $this->controladorMensajeria->mostrarNotificaciones();
            return;
        }
        
        if ($ruta === 'obtener_historial_chat' && isset($_GET['id_chat'])) {
            $this->controladorMensajeria->obtenerHistorialChat();
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            if ($ruta === 'procesar_adicion') {
                $this->controladorProducto->procesarAdicion();
                return;
            } elseif ($ruta === 'procesar_edicion') {
                $this->controladorProducto->procesarEdicion();
                return;
            } elseif ($ruta === 'eliminar_producto') {
                $this->controladorProducto->procesarEliminacion();
                return;
            }

            if ($ruta === 'procesar_publicacion') {
                $this->controladorServicio->procesarPublicacion();
                return;
            } 
            elseif ($ruta === 'procesar_edicion_servicio') {
                $this->controladorServicio->procesarEdicion();
                return;
            } 
            elseif ($ruta === 'procesar_eliminacion_servicio') {
                $this->controladorServicio->procesarEliminacion();
                return;
            } 
            elseif ($ruta === 'procesar_eliminacion_admin') {
                $this->controladorAdmin->procesarEliminacionServicio();
                return;
            }
            
            elseif ($ruta === 'procesar_bloqueo_usuario') {
                $this->controladorAdmin->procesarBloqueoUsuario();
                return;
            }
            elseif ($ruta === 'procesar_desbloqueo_usuario') {
                $this->controladorAdmin->procesarDesbloqueoUsuario();
                return;
            }

            elseif ($ruta === 'procesar_reseña_servicio') { 
                $this->controladorResena->procesarReseñaServicio(); 
                return;
            } 
            elseif ($ruta === 'procesar_reseña_web') { 
                $this->controladorResena->procesarReseñaWeb();
                return;
            }
            
            elseif ($ruta === 'procesar_registro') {
                $this->controladorUsuario->procesarRegistro();
                return;
            } elseif ($ruta === 'procesar_login') {
                $this->controladorUsuario->procesarLogin();
                return;
            } 
            elseif ($ruta === 'procesar_edicion_perfil') {
                $this->controladorUsuario->procesarEdicionPerfil();
                return;
            } 
            elseif ($ruta === 'eliminar_cuenta') {
                $this->controladorUsuario->procesarEliminarCuenta();
                return;
            }
            
            elseif ($ruta === 'procesar_solicitud_chat') {
                $this->controladorMensajeria->procesarSolicitudChat();
                return;
            } 
            elseif ($ruta === 'procesar_aceptar_chat') {
                $this->controladorMensajeria->procesarAceptarChat();
                return;
            } elseif ($ruta === 'procesar_rechazar_chat') {
                $this->controladorMensajeria->procesarRechazarChat();
                return;
            } elseif ($ruta === 'procesar_enviar_mensaje') {
                $this->controladorMensajeria->procesarEnviarMensaje();
                return;
            } 
            
            elseif ($ruta === 'procesar_solicitud_reserva') {
                $this->controladorReserva->procesarSolicitudReserva(); 
                return;
            } elseif ($ruta === 'procesar_aceptar_reserva') {
                $this->controladorReserva->procesarAceptarReserva();
                return;
            } elseif ($ruta === 'procesar_rechazar_reserva') {
                $this->controladorReserva->procesarRechazarReserva();
                return;
            }
        }
        
        $productosDestacados = $this->modeloProductos->obtenerProductosDestacados(6);
        $productosMasVendidos = $this->modeloProductos->obtenerProductosMasVendidos(6);
        
        require_once 'vista/plantilla/encabezado.php';
        require_once 'vista/principal/inicio.php';
        require_once 'vista/plantilla/pie_pagina.php';
    }
}