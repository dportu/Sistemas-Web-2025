<?php
namespace es\ucm\fdi\aw\compras;

use es\ucm\fdi\aw\MagicProperties;
use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\eventos\Evento;



class Compra {
    use MagicProperties;

    private $conn;

    private $id;
    private $username;
    private $evento_id;
    private $cantidad;
    private $precio_unitario;
    private $puntos_usados;
    private $fecha_compra;

    //  CONSTRUCTOR
    private function __construct($id, $username, $evento_id, $cantidad, $precio_unitario, $puntos_usados, $fecha_compra) {
        $this->id = $id;
        $this->username = $username;
        $this->evento_id = $evento_id;
        $this->cantidad = $cantidad;
        $this->precio_unitario = $precio_unitario;
        $this->puntos_usados = $puntos_usados;
        $this->fecha_compra = $fecha_compra;

        $this->conn = Aplicacion::getInstance()->getConexionBd();
    }

    public static function comprar ($conn, $usuario, $evento, $cantidad, $precio_unitario, $descuento) {
        $query = sprintf(
            "INSERT INTO compras (usuario, evento_id, cantidad, precio_unitario, puntos_usados) 
            VALUES ('%s', %d, %d, %.2f, %d)",
            $conn->real_escape_string($usuario->getUsername()),
            $evento->getId(),
            $cantidad,
            $precio_unitario,
            $descuento
        );
        
        if (!$conn->query($query)) {
            error_log("Error BD: " . $conn->error);
            $compraExitosa = false;
        }
        else {
            $compraExitosa = true;
        }

        return $compraExitosa;
    }

    public static function getEntradasUsuario($username) {
        $compras = Compra::comprasUsuario($username);
        $entradasPorEvento = [];
    
        foreach ($compras as $compra) {
            $eventoId = $compra->getEventoId();
            $cantidad = $compra->getCantidad();
    
            if (!isset($entradasPorEvento[$eventoId])) {
                $entradasPorEvento[$eventoId] = 0;
            }
    
            $entradasPorEvento[$eventoId] += $cantidad;
        }
    
        return $entradasPorEvento; //diccionario con clave id_evento y valor numero de entradas compradas por el usuario
    }
    
    
    //finalmente no se usa
    private static function eventosUsuario($username) {
        $conexion = Aplicacion::getInstance()->getConexionBd();
        $username = $conexion->real_escape_string($username);

        //obtenemos todos los evento_id comprados por el usuario
        $query = sprintf("SELECT DISTINCT evento_id FROM compras WHERE usuario='%s'", $username);
        $result = $conexion->query($query);

        $eventos = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $evento = Evento::buscaPorId($row['evento_id']);
                if ($evento) {
                    $eventos[] = $evento;
                }
            }
            $result->free();
        }

        return $eventos; //array de objetos Evento
        
    }

    public static function comprasUsuario($username) {
        $conexion = Aplicacion::getInstance()->getConexionBd();

        $query = sprintf("SELECT * FROM compras WHERE usuario='%s'" , 
        $conexion->real_escape_string($username));

        $result = $conexion->query($query);

        $compras = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $compras[] = new Compra(
                    $row['id'], 
                    $row['usuario'], 
                    $row['evento_id'], 
                    $row['cantidad'], 
                    $row['precio_unitario'], 
                    $row['puntos_usados'], 
                    $row['fecha_compra']
                );
            }
            $result->free();
        }

        return $compras;
    }

    public static function getCompras() {
        $conexion = Aplicacion::getInstance()->getConexionBd();
        $query = "SELECT * FROM compras";
        $result = $conexion->query($query);

        $compras = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $compras[] = new Compra(
                    $row['id'], 
                    $row['usuario'], 
                    $row['evento_id'], 
                    $row['cantidad'], 
                    $row['precio_unitario'], 
                    $row['puntos_usados'], 
                    $row['fecha_compra']
                );
            }
            $result->free();
        }

        return $compras; // devolvemos el array con todos los eventos
    }


    //GETTERS

    public function getId() {
        return $this->id;
    }
    
    public function getUsername() {
        return $this->username;
    }
    
    public function getEventoId() {
        return $this->evento_id;
    }
    
    public function getCantidad() {
        return $this->cantidad;
    }
    
    public function getPrecioUnitario() {
        return $this->precio_unitario;
    }
    
    public function getPuntosUsados() {
        return $this->puntos_usados;
    }
    
    public function getFechaCompra() {
        return $this->fecha_compra;
    }
    

}