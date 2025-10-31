<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . '/'); 
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once ROOT_PATH . 'controlador/ControladorPrincipal.php';

$controlador = new ControladorPrincipal();
$controlador->manejarPeticion();

?>