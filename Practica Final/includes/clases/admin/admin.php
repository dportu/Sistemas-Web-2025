<?php
namespace es\ucm\fdi\aw\admin;

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\MagicProperties;
use es\ucm\fdi\aw\usuarios\Usuario;
use es\ucm\fdi\aw\eventos\Evento;

class Admin extends Usuario
{
    public function __construct($username, $password, $email, $rol = self::ADMIN_ROLE)
    {
        parent::__construct($username, $password, $email, $rol);
    }

    /**
     * Crea un nuevo evento en la base de datos
     */
    public static function crearEvento($datos) {
        if (!Aplicacion::getInstance()->tieneRol('administrador')) {
            throw new \Exception("Acceso denegado");
        }
        return Evento::altaEvento(
            $datos['nombreEvento'], 
            $datos['precio'], 
            $datos['descripcion'], 
            $datos['fecha'], 
            $datos['ubicacion'], 
            $datos['organizador'], 
            $datos['imagen']
        );
    }

    public static function eliminarEvento($idEvento) {
        $evento = Evento::buscaPorId($idEvento);
        if ($evento && Aplicacion::getInstance()->tieneRol('administrador')) {
            return $evento->eliminarEvento();
        }
        return false;
    }

    /**
     * Actualiza un evento 
     */
    public static function actualizarEvento($id_evento, $campos)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        
        $set = [];
        foreach ($campos as $key => $value) {
            $set[] = "$key = '" . $conn->real_escape_string($value) . "'";
        }
        
        $id_evento = $conn->real_escape_string($id_evento);
        $query = "UPDATE eventos SET " . implode(', ', $set) . " WHERE id = $id_evento";
        return $conn->query($query);
    }

    
    /**
     * Edita cualquier mensaje del foro admin puede editar todos
     */
    public static function editarMensaje($id_mensaje, $nuevoContenido)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        
        $query = sprintf(
            "UPDATE foro SET mensaje = '%s' WHERE id = %d",
            $conn->real_escape_string($nuevoContenido),
            $id_mensaje
        );
        
        return $conn->query($query);
    }


    public static function eliminarMensaje($id_mensaje)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $id_mensaje = $conn->real_escape_string($id_mensaje);
        return $conn->query("DELETE FROM foro WHERE id = $id_mensaje");
    }

    
    public function puedeGestionarEventos()
    {
        return $this->tieneRol(self::ADMIN_ROLE) || $this->tieneRol(self::PROMOTOR_ROLE);
    }
    
    public function puedeModerarContenido()
    {
        return $this->tieneRol(self::ADMIN_ROLE);
    }
}
?>