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
    private $entradas;

    //  CONSTRUCTOR
    private function __construct($id, $nombre, $precio, $descripcion, $fecha_inicio, $ubicacion, $organizador, $imagen, $entradas) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->descripcion = $descripcion;
        $this->fecha_inicio = $fecha_inicio;
        $this->ubicacion = $ubicacion;
        $this->organizador = $organizador;
        $this->imagen = $imagen;
        $this->entradas = $entradas;

        $this->conn = Aplicacion::getInstance()->getConexionBd();
    }


    //  METODOS PUBLICOS
    //  ESTATICOS

    public static function getNombrePorId($id) {
        $evento = Evento::buscaPorId($id);
        $ret = null;
        if($evento) {
            $ret = $evento->getNombre();
        }
        return $ret;
    }
    public static function altaEvento($nombre, $precio, $descripcion, $fecha_inicio, $ubicacion, $organizador, $imagen, $entradas) {
        $evento = Evento::buscaPorNombre($nombre);
        if($evento != null) {
            $mensaje = "Ya existe un evento con ese nombre";
            $ret = false;
        }
        else {
            $eventoId = Evento::insertarEvento($nombre, $precio, $descripcion, $fecha_inicio, $ubicacion, $organizador, $imagen, $entradas);
            if($eventoId != false) {
                new Evento($eventoId, $nombre, $precio, $descripcion, $fecha_inicio, $ubicacion, $organizador, $imagen, $entradas);
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
    
    
    public static function getIds() {
        $conexion = Aplicacion::getInstance()->getConexionBd();
        $query = "SELECT id FROM eventos";
        $result = $conexion->query($query);

        $ids = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $ids[] = $row['id'];
            }
            $result->free();
        }

        return $ids;
    }
    public static function getEventos() {
        //return: array con todos los eventos en la base de datos actualmente

        $conexion = Aplicacion::getInstance()->getConexionBd();
        $query = "SELECT id, nombre, precio, descripcion, fecha_inicio, ubicacion, organizador, imagen, entradas FROM eventos";
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
                    $row['imagen'],
                    $row['entradas']
                );
            }
            $result->free();
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
        $sql = "SELECT * FROM eventos WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $result = false;

        if ($stmt) {
            $stmt->bind_param("i", $idEvento);
            if ($stmt->execute()) {
                $rs = $stmt->get_result();
                if ($fila = $rs->fetch_assoc()) {
                    $result = new Evento(
                        $idEvento,
                        $fila['nombre'],
                        $fila['precio'],
                        $fila['descripcion'],
                        $fila['fecha_inicio'],
                        $fila['ubicacion'],
                        $fila['organizador'],
                        $fila['imagen'],
                        $fila['entradas']
                    );
                }
                $rs->free();
            } else {
                error_log("Error al ejecutar la consulta: " . $stmt->error);
            }
            $stmt->close();
        } else {
            error_log("Error al preparar la consulta: " . $conexion->error);
        }
        return $result;
    }

    public static function compra($id_evento, $usuario, $precio, $cantidad) {
        $evento = Evento::buscaPorId($id_evento);
        
        if ($evento && $evento->entradas >= $cantidad) {
            $evento->actualizaEntradas($cantidad);
            $puntos = ($precio * $cantidad) * 0.25; // 25% del precio total
            $usuario->addPuntos($puntos);
            Usuario::actualiza($usuario);
            return true;
        }
        return false;
    }

    //  INSTANCIADOS
    public function editarEvento($nombre, $precio, $descripcion, $fecha_inicio, $ubicacion, $organizador, $imagen, $entradas) {
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->descripcion = $descripcion;
        $this->fecha_inicio = $fecha_inicio;
        $this->ubicacion = $ubicacion;
        $this->organizador = $organizador;
        $this->imagen = $imagen;
        $this->entradas = $entradas;
    
        $sql = "UPDATE eventos SET nombre=?, precio=?, descripcion=?, fecha_inicio=?, ubicacion=?, organizador=?, imagen=?, entradas=? WHERE id=?";
    
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Error en preparación: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("sdsssssii",
            $this->nombre, 
            $this->precio, 
            $this->descripcion, 
            $this->fecha_inicio, 
            $this->ubicacion, 
            $this->organizador, 
            $this->imagen,
            $this->entradas,
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
            error_log("Error en la preparación de la consulta: " . $this->conn->error);
            return false;
        }

        //vinculamos los parametros
        $stmt->bind_param("i", $this->id);

        if (!$stmt->execute()) {
            error_log("Error al ejecutar la eliminación: " . $stmt->error);
            return false;
        }   
        
        $stmt->close();
        return true;
    }


    public function actualizaEntradas($cantidad) {
        $sql = "UPDATE eventos SET entradas = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $nuevoStock = $this->entradas - $cantidad;
        $stmt->bind_param("ii", $nuevoStock, $this->id);
        return $stmt->execute();
    }

    
    //METODOS PRIVADOS

    //solo puede ser usada en altaevento
    private static function insertarEvento($nombre, $precio, $descripcion, $fecha_inicio, $ubicacion, $organizador, $imagen, $entradas) { //creo que hay que modificar mas cosas para adaptar al nuevo parametro entradas
        $conexion = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("INSERT INTO eventos (nombre, precio, descripcion, fecha_inicio, ubicacion, organizador, imagen, entradas) 
                    VALUES ('%s', %f, '%s', '%s', '%s', '%s', '%s', %d)",
            $conexion->real_escape_string($nombre),
            $conexion->real_escape_string($precio),
            $conexion->real_escape_string($descripcion),
            $conexion->real_escape_string($fecha_inicio),
            $conexion->real_escape_string($ubicacion),
            $conexion->real_escape_string($organizador),
            $conexion->real_escape_string($imagen),
            $conexion->real_escape_string($entradas)
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

    public function getEntradasDisponibles() {
        return $this->entradas;
    }
}