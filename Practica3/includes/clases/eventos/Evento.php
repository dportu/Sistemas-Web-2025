<?php
namespace es\ucm\fdi\aw\eventos;

use es\ucm\fdi\aw\MagicProperties;
use es\ucm\fdi\aw\Aplicacion; //import de aplicacion?

class Evento {
    use MagicProperties;

    private $conn;

    private $id;
    private $nombreEvento;
    private $precio;
    private $descripcion;
    private $fecha;
    private $ubicacion;
    private $organizador;
    private $imagen;

    //  CONSTRUCTOR
    private function __construct($id, $nombreEvento, $precio, $descripcion, $fecha, $ubicacion, $organizador, $imagen) {
        $this->id = $id;
        $this->nombreEvento = $nombreEvento;
        $this->precio = $precio;
        $this->descripcion = $descripcion;
        $this->fecha = $fecha;
        $this->ubicacion = $ubicacion;
        $this->organizador = $organizador;

        $this->conn = Aplicacion::getInstance()->getConexionBd();
    }


    //  METODOS PUBLICOS
    //  ESTATICOS
    public static function altaEvento($nombreEvento, $precio, $descripcion, $fecha, $ubicacion, $organizador, $imagen) {
        $evento = Evento::buscaPorNombre($nombreEvento);
        if($evento != null) {
            $mensaje = "Ya existe un evento con ese nombre";
            $ret = false;
        }
        else {
            $eventoId = Evento::insertarEvento($nombreEvento, $precio, $descripcion, $fecha, $ubicacion, $organizador, $imagen);
            if($eventoId != false) {
                new Evento($eventoId, $nombreEvento, $precio, $descripcion, $fecha, $ubicacion, $organizador, $imagen);
                $mensaje = "Evento dado de alta con éxito";
                $ret = true;
            }
            else {
                $mensaje = "Error al dar de alta el evento";
                $ret = false;
            }
        }

        //Mostramos un mensaje de éxito o de error
        echo "<script>alert('$mensaje');</script>";
        return $ret; //devolvemos si se ha realizado el alta o no
    }
    
    
    public static function getEventos() {
        //return: array con todos los eventos en la base de datos actualmente

        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = "SELECT id, nombreEvento, precio, descripcion, fecha, ubicacion, organizador, imagen FROM eventos";
        $result = $conn->query($query);

        $eventos = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $eventos[] = new Evento(
                    $row['id'], 
                    $row['nombreEvento'], 
                    $row['precio'], 
                    $row['descripcion'], 
                    $row['fecha'], 
                    $row['ubicacion'], 
                    $row['organizador'], 
                    $row['imagen']
                );
            }
        }

        return $eventos; // devolvemos el array con todos los eventos
    }

    public static function buscaPorNombre($nombreEvento) {
        //modificacion sobre buscaUsuario

        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM eventos WHERE nombreEvento='%s'", $conn->real_escape_string($nombreEvento));
        $rs = $conn->query($query);
        $result = false;
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $result = $fila['id'];
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    public static function buscaPorId($idEvento) {
        //modificacion sobre buscaPorId
        
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM eventos WHERE id=%d", $idEvento);
        $rs = $conn->query($query);
        $result = false;
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $result = new Evento($idEvento, $fila['nombreEvento'], $fila['precio'], $fila['descripcion'], $fila['fecha'], $fila['ubicacion'], $fila['organizador'], $fila['imagen']);
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    public static function compra($evento, $usuario, $precio, $cantidad) { //parametros de entrada provisionales
        $id = Evento::buscaPorNombre($evento); //se podria simplificar a busqueda solo por nombre
        if (Evento::buscaPorId($id)) { //comprobar tambien que queden entradas y reducirlas?
            //$usuario->addPuntos($precio / 4); se añadiria los puntos desde usuario?
            $ret = true;
            $mensaje = "¡Operación realizada con éxito!";
        }
        else {
            $ret = false;
            $mensaje = "Error en la compra";
        }

        //Mostramos un mensaje de éxito o de error
        echo "<script>alert('$mensaje');</script>";
        return $ret; //devolvemos si se ha realizado la compra o no
    }

    //  INSTANCIADOS
    public function editarEvento($nombreEvento, $precio, $descripcion, $fecha, $ubicacion, $organizador, $imagen) {
        $this->nombreEvento = $nombreEvento;
        $this->precio = $precio;
        $this->descripcion = $descripcion;
        $this->fecha = $fecha;
        $this->ubicacion = $ubicacion;
        $this->organizador = $organizador;
        $this->imagen = $imagen;

        $sql = "UPDATE eventos SET nombreEvento=?, precio=?, descripcion=?, fecha=?, ubicacion=?, organizador=?, imagen=? WHERE id=?";

        //preparamos la insercion
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $this->conn->error);
        }

        //vinculamos los parametros
        $stmt->bind_param("sdsssssi",
            $this->nombreEvento, 
            $this->precio, 
            $this->descripcion, 
            $this->fecha, 
            $this->ubicacion, 
            $this->organizador, 
            $this->imagen,
            $this->id
        );

        if (!$stmt->execute()) {
            die("Error al modificar evento: " . $stmt->error);
        }
        
        $stmt->close(); //hay que cerrar ?
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
    private static function insertarEvento($nombreEvento, $precio, $descripcion, $fecha, $ubicacion, $organizador, $imagen) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("INSERT INTO eventos (nombreEvento, precio, descripcion, fecha, ubicacion, organizador, imagen) VALUES ('%s', %d, '%s', '%s', '%s', '%s', '%s')",
            $conn->real_escape_string($nombreEvento),
            $precio,
            $conn->real_escape_string($descripcion),
            $conn->real_escape_string($fecha),
            $conn->real_escape_string($ubicacion),
            $conn->real_escape_string($organizador),
            $conn->real_escape_string($imagen)
        );
        if ($conn->query($query)) {
            return $conn->insert_id;
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
            return false;
        }
    }

    
    
    

    

    

    

}