<?php
namespace es\ucm\fdi\aw\productos;

use es\ucm\fdi\aw\MagicProperties;

class Evento {
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

    __construct() {
        $conn = Aplicacion::getInstance()->getConexionBd();
    }

}