<?php
namespace es\ucm\fdi\aw\productos;

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\MagicProperties;

class Valoraciones {
    use MagicProperties;

    private $conn;

    private $id_evento;
    private $username;
    private $nota;
    private $comentario;
    private $fecha;

    /*
        TODO: Añadir los métodos necesarios para gestionar las valoraciones de los eventos.
        - Crear una valoración (insertar en la base de datos)
        - Obtener una valoración (consultar en la base de datos)
        - Actualizar una valoración (actualizar en la base de datos)
        - Borrar una valoración (borrar en la base de datos)
        - Obtener todas las valoraciones de un evento (consultar en la base de datos)
    */

    function __construct($id_evento, $username, $nota, $comentario, $fecha) {
        $this->id_evento = $id_evento;
        $this->username = $username;
        $this->nota = $nota;
        $this->comentario = $comentario;
        $this->fecha = $fecha;

        $this->conn = Aplicacion::getInstance()->getConexionBd();
    }

}