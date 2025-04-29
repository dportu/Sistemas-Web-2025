<?php
namespace es\ucm\fdi\aw\valoraciones;

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\MagicProperties;

class Valoracion {
    use MagicProperties;

    private $conn;

    private $id_evento;
    private $username;
    private $nota;
    private $comentario;
    private $fecha;


    //  GETTERS
    public function getIdEvento() {
        return $this->id_evento;
    }
    public function getUsername() {
        return $this->username;
    }
    public function getNota() {
        return $this->nota;
    }
    public function getComentario() {
        return $this->comentario;
    }
    public function getFecha() {
        return $this->fecha;
    }

    /*
        TODO: Añadir los métodos necesarios para gestionar las valoraciones de los eventos.
        - Crear una valoración (insertar en la base de datos)
        - Obtener una valoración (consultar en la base de datos)
        - Actualizar una valoración (actualizar en la base de datos)
        - Borrar una valoración (borrar en la base de datos)
        - Obtener todas las valoraciones de un evento (consultar en la base de datos)
    */

    //  CONSTRUCTOR
    function __construct($id_evento, $username, $nota, $comentario, $fecha) {
        $this->id_evento = $id_evento;
        $this->username = $username;
        $this->nota = $nota;
        $this->comentario = $comentario;
        $this->fecha = $fecha;

        $this->conn = Aplicacion::getInstance()->getConexionBd();
    }

    public static function notaMedia($evento) {
        $valoraciones = Valoracion::valoracionesEvento($evento);
        $notaTotal = 0;

        if (count($valoraciones) === 0) {
            return 0; // O null, o mostrar un mensaje, según tu lógica
        }

        for($i = 0; $i< count($valoraciones); $i++) {
            $notaTotal += $valoraciones[$i]->getNota();
        }

        return $notaTotal / $i;
    }

    public static function valoracionesEvento($evento) {

        $conexion = Aplicacion::getInstance()->getConexionBd();
        $idEvento = $evento->getId(); // Get event ID
        $query = "SELECT * FROM valoraciones WHERE id_evento = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("i", $idEvento);
        $stmt->execute();
        $result = $stmt->get_result();

        $valoraciones = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $valoraciones[] = new Valoracion(
                    $row['id_evento'], 
                    $row['username'], 
                    $row['nota'], 
                    $row['comentario'], 
                    $row['fecha']
                );
            }
        }

        return $valoraciones; // devolvemos el array con todas las valoraciones
    }

    public static function getValoraciones() {
        $conexion = Aplicacion::getInstance()->getConexionBd();
        $query = "SELECT * FROM valoraciones";
        $result = $conexion->query($query);

        $valoraciones = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $valoraciones[] = new Valoracion(
                    $row['id_evento'], 
                    $row['username'], 
                    $row['nota'], 
                    $row['comentario'], 
                    $row['fecha']
                );
            }
        }

        $result->free();

        return $valoraciones; // devolvemos el array con todas las valoraciones
    }
}
