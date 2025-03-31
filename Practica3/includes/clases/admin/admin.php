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

    // ======================== MÉTODOS PARA EVENTOS ========================
    
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
     * Actualiza un evento existente
     */
    public static function actualizarEvento($id_evento, $campos)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        
        $set = [];
        foreach ($campos as $key => $value) {
            $set[] = "$key = '" . $conn->real_escape_string($value) . "'";
        }
        
        $query = "UPDATE eventos SET " . implode(', ', $set) . " WHERE id = $id_evento";
        return $conn->query($query);
    }

    /**
     * Elimina un evento y todos sus mensajes asociados
     */
    public static function eliminarEvento($id_evento)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        
        // Eliminar mensajes asociados primero
        $conn->query("DELETE FROM foro WHERE evento = $id_evento");
        
        // Eliminar el evento
        return $conn->query("DELETE FROM eventos WHERE id = $id_evento");
    }

    // ======================== MÉTODOS PARA MENSAJES ========================
    
    /**
     * Edita cualquier mensaje del foro (admin puede editar todos)
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

    /**
     * Elimina cualquier mensaje del foro
     */
    public static function eliminarMensaje($id_mensaje)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        return $conn->query("DELETE FROM foro WHERE id = $id_mensaje");
    }

    // ======================== VERIFICACIÓN DE PERMISOS ========================
    
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