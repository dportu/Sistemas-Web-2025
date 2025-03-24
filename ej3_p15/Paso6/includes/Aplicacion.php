<?php
/**
 * Clase que mantiene el estado global de la aplicación.
 */
class Aplicacion {
    private static $instancia;
    private $bdDatosConexion;
    private $inicializada = false;
    private $conn;

    private function __construct() {}

    public static function getInstance() {
        if (!self::$instancia instanceof self) {
            self::$instancia = new static();
        }
        return self::$instancia;
    }

    public function init($bdDatosConexion) {
        if (!$this->inicializada) {
            $this->bdDatosConexion = $bdDatosConexion;
            $this->inicializada = true;
           
            
            // Configuración adicional desde tu config.php original
            ini_set('default_charset', 'UTF-8');
            setlocale(LC_ALL, 'es_ES.UTF.8');
            date_default_timezone_set('Europe/Madrid');
        }
    }

    public function shutdown() {
        $this->compruebaInstanciaInicializada();
        if ($this->conn !== null && !$this->conn->connect_errno) {
            $this->conn->close();
        }
    }

    private function compruebaInstanciaInicializada() {
        if (!$this->inicializada) {
            die("Aplicación no inicializada");
        }
    }

    public function getConexionBd() {
        $this->compruebaInstanciaInicializada();
        if (!$this->conn) {
            $conn = new mysqli(
                $this->bdDatosConexion['host'],
                $this->bdDatosConexion['user'],
                $this->bdDatosConexion['pass'],
                $this->bdDatosConexion['bd']
            );

            if ($conn->connect_errno) {
                die("Error de conexión: ({$conn->connect_errno}) {$conn->connect_error}");
            }

            if (!$conn->set_charset("utf8mb4")) {
                die("Error al configurar charset: ({$conn->errno}) {$conn->error}");
            }

            $this->conn = $conn;
        }
        return $this->conn;
    }
}
?>