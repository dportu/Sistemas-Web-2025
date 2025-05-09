<?php
namespace es\ucm\fdi\aw\valoraciones;

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\MagicProperties;

class Valoracion {
    use MagicProperties;

    private $conn;

    private $id;
    private $id_evento;
    private $username;
    private $nota;
    private $comentario;
    private $fecha;


    //  GETTERS
    public function getId() {
        return $this->id;
    }
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

    //  CONSTRUCTOR
    function __construct($id, $id_evento, $username, $nota, $comentario, $fecha) {
        $this->id = $id;
        $this->id_evento = $id_evento;
        $this->username = $username;
        $this->nota = $nota;
        $this->comentario = $comentario;
        $this->fecha = $fecha;

        $this->conn = Aplicacion::getInstance()->getConexionBd();
    }

    public static function notaMedia($id_evento) {
        $valoraciones = Valoracion::valoracionesEvento($id_evento);
        $notaTotal = 0;

        if (count($valoraciones) === 0) {
            return 0;
        }

        for($i = 0; $i< count($valoraciones); $i++) {
            $notaTotal += $valoraciones[$i]->getNota();
        }

        return $notaTotal / $i;
    }

    public static function eventoValoradoPorUsuario($username, $id_evento) {
        $valoraciones = Valoracion::valoracionesEvento($id_evento);
        $found = false;

        foreach ($valoraciones as $valoracion) {
            if ($valoracion->getUsername() === $username) {
                $found = true;
            }
        }

        return $found;
    }

    public static function valoracionesEvento($id_evento = null) {
        $conexion = Aplicacion::getInstance()->getConexionBd();
        $query = $id_evento 
        ? "SELECT * FROM valoraciones WHERE id_evento = ?" 
        : "SELECT * FROM valoraciones";   // con esto conseguimos poder mostralos si el id es nuLL, esto es para la vista de admin
       
       
        $stmt = $conexion->prepare($query);

        if ($id_evento) {   // esto para las que hay
            $id_evento = $conexion->real_escape_string($id_evento);
            $stmt->bind_param("i", $id_evento);
        }
   
        $stmt->execute();
        $result = $stmt->get_result();

        $valoraciones = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $valoraciones[] = new Valoracion(
                    $row['id'],
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
    
    public static function getValoracionPorId($id) {
        $conexion = Aplicacion::getInstance()->getConexionBd();
        $query = "SELECT * FROM valoraciones WHERE id = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();
            return new Valoracion(
                $row['id'], 
                $row['id_evento'], 
                $row['username'], 
                $row['nota'], 
                $row['comentario'], 
                $row['fecha']
            );
        } 
        return null;
    }

    public static function insertarValoracion($id_evento, $username, $nota, $comentario) {
        $conexion = Aplicacion::getInstance()->getConexionBd();
        $query = "INSERT INTO valoraciones (id_evento, username, nota, comentario, fecha) VALUES (?, ?, ?, ?, NOW())";
        
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("isis", $id_evento, $username, $nota, $comentario);
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $conexion->error);
        }

        if (!$stmt->execute()) {
            die("Error al insertar valoracion: " . $stmt->error);
        }

        $conexion->insert_id;

        $stmt->close();
        
        return true;
    }

    public static function editarValoracion($id, $nota, $comentario) {
        $conexion = Aplicacion::getInstance()->getConexionBd();
        
        $query = "UPDATE valoraciones SET nota = ?, comentario = ?, fecha = NOW() WHERE id = ?";
    
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("isi", $nota, $comentario, $id);
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $conexion->error);
        }
        if (!$stmt->execute()) {
            die("Error al editar valoracion: " . $stmt->error);
        }
        $stmt->close();
        return true;
    }

    public static function eliminarValoracion($id) {
        $conexion = Aplicacion::getInstance()->getConexionBd();
        $query = "DELETE FROM valoraciones WHERE id = ?";

        $stmt = $conexion->prepare($query);
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $conexion->error);
        }

        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            die("Error al eliminar la valoración: " . $stmt->error);
        }

        $stmt->close();
        return true;
    }
}
