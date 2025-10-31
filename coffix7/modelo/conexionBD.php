<?php

class ModeloConexion {
    private static $instancia = null;
    private $servidor = "localhost";
    private $usuario = "root"; 
    private $contrasena = ""; 
    private $bd = "coffix_bd";
    private $pdo;

    private function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->servidor};dbname={$this->bd};charset=utf8mb4", 
                $this->usuario, 
                $this->contrasena
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            die("❌ Error de conexión a la base de datos.");
        }
    }

    public static function obtenerInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    public function obtenerPDO() {
        return $this->pdo;
    }

    private function __clone() {}
    public function __wakeup() {}
}
?>