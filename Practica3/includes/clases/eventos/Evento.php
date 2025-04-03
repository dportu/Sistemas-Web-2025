<?php
namespace es\ucm\fdi\aw\eventos;

use es\ucm\fdi\aw\MagicProperties;
use es\ucm\fdi\aw\Aplicacion; //import de aplicacion?
use es\ucm\fdi\aw\usuarios\Usuario;

class Evento {
    use MagicProperties;

    private $conn;

    private $id;
    private $nombre;
    private $precio;
    private $descripcion;
    private $fecha_inicio;
    private $ubicacion;
    private $organizador;
    private $imagen;

    //  CONSTRUCTOR
    private function __construct($id, $nombre, $precio, $descripcion, $fecha_inicio, $ubicacion, $organizador, $imagen) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->descripcion = $descripcion;
        $this->fecha_inicio = $fecha_inicio;
        $this->ubicacion = $ubicacion;
        $this->organizador = $organizador;
        $this->imagen = $imagen;

        $this->conn = Aplicacion::getInstance()->getConexionBd();
    }


    //  METODOS PUBLICOS
    //  ESTATICOS
    public static function altaEvento($nombre, $precio, $descripcion, $fecha_inicio, $ubicacion, $organizador, $imagen) {
        $evento = Evento::buscaPorNombre($nombre);
        if($evento != null) {
            $mensaje = "Ya existe un evento con ese nombre";
            $ret = false;
        }
        else {
            $eventoId = Evento::insertarEvento($nombre, $precio, $descripcion, $fecha_inicio, $ubicacion, $organizador, $imagen);
            if($eventoId != false) {
                new Evento($eventoId, $nombre, $precio, $descripcion, $fecha_inicio, $ubicacion, $organizador, $imagen);
                $mensaje = "Evento dado de alta con éxito";
                $ret = true;
            }
            else {
                $mensaje = "Error al dar de alta el evento";
                $ret = false;
            }
        }

        return [$ret, $mensaje]; //devolvemos si se ha realizado el alta o no
    }
    
    
    public static function getEventos() {
        //return: array con todos los eventos en la base de datos actualmente

        $conexion = Aplicacion::getInstance()->getConexionBd();
        $query = "SELECT id, nombre, precio, descripcion, fecha_inicio, ubicacion, organizador, imagen FROM eventos";
        $result = $conexion->query($query);

        $eventos = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $eventos[] = new Evento(
                    $row['id'], 
                    $row['nombre'], 
                    $row['precio'], 
                    $row['descripcion'], 
                    $row['fecha_inicio'], 
                    $row['ubicacion'], 
                    $row['organizador'], 
                    $row['imagen']
                );
            }
        }

        return $eventos; // devolvemos el array con todos los eventos
    }

    public static function buscaPorNombre($nombre) {
        //modificacion sobre buscaUsuario

        $conexion = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM eventos WHERE nombre='%s'", $conexion->real_escape_string($nombre));
        $rs = $conexion->query($query);
        $result = false;
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $result = $fila['id'];
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conexion->errno}): {$conexion->error}");
        }
        return $result;
    }

    public static function buscaPorId($idEvento) {
        //modificacion sobre buscaPorId
        
        $conexion = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM eventos WHERE id=%d", $idEvento);
        $rs = $conexion->query($query);
        $result = false;
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $result = new Evento($idEvento, $fila['nombre'], $fila['precio'], $fila['descripcion'], $fila['fecha_inicio'], $fila['ubicacion'], $fila['organizador'], $fila['imagen']);
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conexion->errno}): {$conexion->error}");
        }
        return $result;
    }

    public static function compra($id_evento, $usuario, $precio, $cantidad) { //parametros de entrada provisionales
        if (Evento::buscaPorId($id_evento)) { //comprobar tambien que queden entradas y reducirlas?
            $ret = true;
        }
        else {
            $ret = false;
        }

        Usuario::actualiza($usuario);

        return $ret; //devolvemos booleano de exito o error
    }

    //  INSTANCIADOS
    public function editarEvento($nombre, $precio, $descripcion, $fecha_inicio, $ubicacion, $organizador, $imagen) {
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->descripcion = $descripcion;
        $this->fecha_inicio = $fecha_inicio;
        $this->ubicacion = $ubicacion;
        $this->organizador = $organizador;
        $this->imagen = $imagen;
    
        $sql = "UPDATE eventos SET nombre=?, precio=?, descripcion=?, fecha_inicio=?, ubicacion=?, organizador=?, imagen=? WHERE id=?";
    
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Error en preparación: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("sdsssssi",
            $this->nombre, 
            $this->precio, 
            $this->descripcion, 
            $this->fecha_inicio, 
            $this->ubicacion, 
            $this->organizador, 
            $this->imagen,
            $this->id
        );
    
        $result = $stmt->execute();
        $stmt->close();
    
        return $result; // Retorna true/false según éxito
    }

    public function eliminarEvento() {
        $sql = "DELETE FROM eventos WHERE id=?";

        //preparamos la insercion
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $this->conn->error);
        }

        //vinculamos los parametros
        $stmt->bind_param("i", $this->id);

        if (!$stmt->execute()) {
            die("Error al eliminar evento: " . $stmt->error);
        }
        
        $stmt->close(); //hay que cerrar ?
    }
    
    //METODOS PRIVADOS

    //solo puede ser usada en altaevento
    private static function insertarEvento($nombre, $precio, $descripcion, $fecha_inicio, $ubicacion, $organizador, $imagen) {
        $conexion = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("INSERT INTO eventos (nombre, precio, descripcion, fecha_inicio, ubicacion, organizador, imagen) VALUES ('%s', %d, '%s', '%s', '%s', '%s', '%s')",
            $conexion->real_escape_string($nombre),
            $precio,
            $conexion->real_escape_string($descripcion),
            $conexion->real_escape_string($fecha_inicio),
            $conexion->real_escape_string($ubicacion),
            $conexion->real_escape_string($organizador),
            $conexion->real_escape_string($imagen)
        );
        if ($conexion->query($query)) {
            return $conexion->insert_id;
        } else {
            error_log("Error BD ({$conexion->errno}): {$conexion->error}");
            return false;
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }
    
    public function getPrecio() {
        return $this->precio;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getFecha() {
        return $this->fecha_inicio;
    }

    public function getUbicacion() {
        return $this->ubicacion;
    }

    public function getOrganizador() {
        return $this->organizador;
    }

    public function getImagen() {
        return $this->imagen;
    }
}