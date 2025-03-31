<?php
namespace es\ucm\fdi\aw\productos;

use es\ucm\fdi\aw\MagicProperties;
use es\ucm\fdi\aw\Aplicacion;

class mensajeForo { //no hay un foro como tal, sino menajes sobre un evento en concreto
    use MagicProperties;

    private $id;
    private $titulo;
    private $autor;
    private $mensaje;
    private $evento;
    private $fecha_publicacion;

    private $conexion;

    /*
        Obtener mensajes
        Crear un mensaje
        Editar un mensaje
        Eliminar un mensaje
    */

    private function __construct($titulo, $autor, $mensaje, $evento, $fecha_publicacion) {
        $this->titulo = $titulo;
        $this->autor = $autor;
        $this->mensaje = $mensaje;
        $this->evento = $evento;
        $this->fecha_publicacion = $fecha_publicacion;

        $this->conexion = Aplicacion::getInstance()->getConexionBd();
    }

    public static function altaMensajeForo($titulo, $autor, $mensaje, $evento, $fecha_publicacion) {
        
    }

    
}

/*<?php


    //pensado para hacer uso del autoincrement de id de la base de datos
    private function __construct($nombreEvento, $precio, $descripcion, $fecha, $ubicacion, $organizador, $imagen) {
        $this->nombreEvento = $nombreEvento;
        $this->precio = $precio;
        $this->descripcion = $descripcion;
        $this->fecha = $fecha;
        $this->ubicacion = $ubicacion;
        $this->organizador = $organizador;

        $this->conn = Aplicacion::getInstance()->getConexionBd();
        
        //la creacion como tal del evento se haria en alta evento
        //$this->id = $this->insertarEvento($conn);
    }

    //solo puede ser usada en altaevento
    private function insertarEvento() {
        $sql = "INSERT INTO eventos (nombreEvento, precio, descripcion, fecha, ubicacion, organizador, imagen) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        //preparamos la insercion
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $this->conn->error);
        }

        //vinculamos los parametros
        $stmt->bind_param("sdsssss", //sdsssss?
            $this->nombreEvento, 
            $this->precio, 
            $this->descripcion, 
            $this->fecha, 
            $this->ubicacion, 
            $this->organizador, 
            $this->imagen
        );

        if (!$stmt->execute()) {
            die("Error al insertar evento: " . $stmt->error);
        }
        
        $ret = $stmt->insert_id;

        $stmt->close(); //hay que cerrar ?

        return $ret;
    }

    public function compra($evento, $usuario, $precio, $cantidad) { //parametros de entrada provisionales
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
    
    public static function altaEvento($nombreEvento, $precio, $descripcion, $fecha, $ubicacion, $organizador, $imagen) {
        $evento = Evento::buscaPorNombre($nombreEvento);
        if($evento != null) {
            $mensaje = "Ya existe un evento con ese nombre";
            $ret = false;
        }
        else {
            new Evento($nombreEvento, $precio, $descripcion, $fecha, $ubicacion, $organizador, $imagen);
            $mensaje = "Evento dado de alta con éxito";
            $ret = true;
        }

        //Mostramos un mensaje de éxito o de error
        echo "<script>alert('$mensaje');</script>";
        return $ret; //devolvemos si se ha realizado el alta o no
    }

    //modificacion sobre buscaUsuario
    public static function buscaPorNombre($nombreEvento) {
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

    //modificacion sobre buscaPorId
    public static function buscaPorId($idEvento) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM eventos WHERE id=%d", $idEvento);
        $rs = $conn->query($query);
        $result = false;
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $result = new Evento($fila['nombreEvento'], $fila['precio'], $fila['descripcion'], $fila['fecha'], $fila['ubicacion'], $fila['organizador'], $fila['imagen']);
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

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

}
