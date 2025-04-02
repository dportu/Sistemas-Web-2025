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
            $imagen = htmlspecialchars($evento->getImagen()); //no es null por defecto
            $nombre = htmlspecialchars($evento->getNombre()); //no puede ser null
            $precio = $evento->getPrecio();
            $fecha = date('d/m/Y H:i', strtotime($evento->getFecha()));

            //ifs para evitar htmlspacialchars(null)
            if($evento->getUbicacion() != null) { 
                $ubicacion = htmlspecialchars($evento->getUbicacion());
            }
            else {
                $ubicacion = "";
            }
            if($evento->getOrganizador() != null) {
                $organizador = htmlspecialchars($evento->getOrganizador());
            }
            else {
                $organizador = "";
            }
            if($evento->getDescripcion() != null) {
                $descripcion = htmlspecialchars($evento->getDescripcion());
            }
            else {
                $descripcion = "";
            }
            
            // Botón de edición solo para admins
            $botonEditar = '';
            if ($app->usuarioLogueado() && $app->tieneRol(Usuario::ADMIN_ROLE)) {
                $botonEditar = "<a href='editar_evento.php?id={$id}' class='boton-editar'>✏️ Editar</a>";
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