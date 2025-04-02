<?php
namespace es\ucm\fdi\aw;

use es\ucm\fdi\aw\eventos\Evento;
use es\ucm\fdi\aw\usuarios\Usuario;
use es\ucm\fdi\aw\Aplicacion;

require_once __DIR__.'/includes/config.php';

$app = Aplicacion::getInstance();
$tituloPagina = 'Eventos';
$contenidoPrincipal = '';

// Obtener ID del evento si existe
$id_evento = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Mostrar listado o detalle según el ID
mostrarEvento($id_evento, $contenidoPrincipal);

// Función para mostrar eventos
function mostrarEvento($id, &$contenidoPrincipal) {
    $app = Aplicacion::getInstance();
    
    if ($id === null) {
        // Mostrar todos los eventos
        $eventos = Evento::getEventos();
        foreach ($eventos as $evento) {
            $imagen = htmlspecialchars($evento->getImagen());
            $nombre = htmlspecialchars($evento->getNombre());
            $precio = $evento->getPrecio();
            $fecha = date('d/m/Y H:i', strtotime($evento->getFecha()));
            
            $contenidoPrincipal .= <<<EOS
                <div class="evento-card">
                    <a href="vistaEvento.php?id={$evento->getId()}">
                        <img src="{$imagen}" alt="{$nombre}" class="evento-imagen">
                        <h3>{$nombre}</h3>
                        <p>{$precio} €</p>
                        <p>{$fecha}</p>
                    </a>
                </div>
            EOS;
        }
    } else {
        // Mostrar detalles de un evento específico
        $evento = Evento::buscaPorId($id);
        if ($evento) {
            $imagen = htmlspecialchars($evento->getImagen());
            $nombre = htmlspecialchars($evento->getNombre());
            $precio = $evento->getPrecio();
            $fecha = date('d/m/Y H:i', strtotime($evento->getFecha()));
            $ubicacion = htmlspecialchars($evento->getUbicacion());
            $organizador = htmlspecialchars($evento->getOrganizador());
            $descripcion = htmlspecialchars($evento->getDescripcion());
            
            // Obtener información del usuario actual
            $usuarioActual = $app->nombreUsuario();
            $esAdmin = $app->tieneRol(Usuario::ADMIN_ROLE);
            $esPromotor = $app->tieneRol('promotor');
            $esOrganizador = ($organizador === $usuarioActual);
            
            // Botón de edición para admins o promotores que son organizadores
            $botonEditar = '';
            if ($app->usuarioLogueado() && ($esAdmin || ($esPromotor && $esOrganizador))) {
                $urlEditar = $app->resuelve('/editar_evento.php?id='.$id);
                $botonEditar = <<<EOS
                    <form action="{$urlEditar}" method="get">
                        <input type="hidden" name="id" value="{$id}">
                        <button type="submit" class="boton-editar">✏️ Editar Evento</button>
                    </form>
                EOS;
            }
            
            $contenidoPrincipal .= <<<EOS
                <div class="evento-detalle">
                    <img src="{$imagen}" alt="{$nombre}" class="evento-imagen-detalle">
                    <div class="info-evento">
                        <h2>{$nombre}</h2>
                        {$botonEditar}
                        <p><strong>Precio:</strong> {$precio} €</p>
                        <p><strong>Fecha:</strong> {$fecha}</p>
                        <p><strong>Ubicación:</strong> {$ubicacion}</p>
                        <p><strong>Organizador:</strong> {$organizador}</p>
                        <p><strong>Descripción:</strong> {$descripcion}</p>
                    </div>
                </div>
            EOS;
        } else {
            $contenidoPrincipal .= "<p class='error'>Evento no encontrado</p>";
        }
    }
}

require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>