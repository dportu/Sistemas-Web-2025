<?php
namespace es\ucm\fdi\aw;

use es\ucm\fdi\aw\eventos\Evento;
use es\ucm\fdi\aw\usuarios\Usuario;
use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\valoraciones\Valoracion; 

require_once __DIR__.'/includes/clases/valoraciones/vistaValoraciones.php';
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
    
    // if ($id === null), en principio lo quitamos
    if ($id === null) {
        // Mostrar todos los eventos
        $eventos = Evento::getEventos();
        foreach ($eventos as $evento) {
            $imagen = htmlspecialchars($evento->getImagen());
            $nombre = htmlspecialchars($evento->getNombre());
            $precio = $evento->getPrecio();
            $fecha = date('d/m/Y H:i', strtotime($evento->getFecha()));
            
            $contenidoPrincipal .= <<<EOS
                <div class="evento">
                    <a href="vistaEvento.php?id={$evento->getId()}">
                        <img src="{$imagen}" alt="{$nombre}" class="evento-icono">
                        <h3>{$nombre}</h3>
                        <p>{$precio} €</p>
                        <p>{$fecha}</p>
                    </a>
                </div>
            EOS;
        }
    } 
    else {
        // Mostrar detalles de un evento específico
        $evento = Evento::buscaPorId($id);
        if ($evento) {
            $imagen = htmlspecialchars($evento->getImagen()); //no es null por defecto
            $nombre = htmlspecialchars($evento->getNombre()); //no puede ser null
            $precio = $evento->getPrecio();
            $fecha = date('d/m/Y H:i', strtotime($evento->getFecha()));
            $entradas = $evento->getEntradasDisponibles();

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


            $usuarioNom = $app->nombreUsuario();
            $usuario = Usuario::buscaUsuario($usuarioNom);
            $esAdmin = $app->tieneRol(Usuario::ADMIN_ROLE);
            $esPromotor = $app->tieneRol(Usuario::PROMOTOR_ROLE);
            $esOrganizador = ($organizador === $usuarioNom);
            
          
            // Botón de edición y de eliminar solo para admins y para los promotores de esos eventos 
            $botonEditar = '';   // Para que no de errores 
            $botonEliminar = '';
            $botonForo = "<a href='foro.php?id={$id}' Foro</a>";

            if (($app->usuarioLogueado()) && ($esAdmin) ||($esPromotor && $esOrganizador)) {
                $botonEliminar = <<<EOS
                <form action="eliminar_evento.php" method="POST" onsubmit="return confirm('¿Estás seguro de querer eliminar este evento?');">
                    <input type="hidden" name="id" value="{$id}">
                    <button type="submit" class="boton eliminar">Eliminar</button>
                </form>
            EOS;
                $botonEditar = "<a href='editar_evento.php?id={$id}' class='boton editar'>Editar</a>";
                
            }

            //  Boton de compra provisional
            $botonCompra = '';
            $params = ['id' => $id];
            $url = $app->buildUrl('foro.php', $params);

            #$url = 'foro.php?id='.$id;
   
            if ($app->usuarioLogueado() && $entradas > 0) {
                $botonCompra = <<<EOS
                <form action="procesarCompra.php" method="GET">
                    <input type="hidden" name="id" value="$id">
                    <button type="submit" class="boton comprar">Comprar</button>
                </form>
            EOS;
            } 
            else if (!$app->usuarioLogueado()) {
                $botonCompra = "<p class='aviso-agotado'>❌ Tienes que estar registrado para poder comprar una entrada. <a href='login.php'> Iniciar sesión </a> </p>";
            }
            else {
                $botonCompra = "<p class='aviso-agotado'>❌ No quedan entradas disponibles</p>";
            }
            $botonForo = <<<EOS
                <form action="foro.php" method="GET">
                    <input type="hidden" name="id" value="$id">
                    <button type="submit" class="boton foro">Foro</button>
                </form>
            EOS;

            $evento = Evento::buscaPorId($id);
            $valoracionMedia = Valoracion::notaMedia($id);
            $valoracionHTML = $valoracionMedia ? "<span class='valoracion-media'>(".number_format($valoracionMedia, 1)." ★)</span>" : "<span class='valoracion-media'>(Sin valoraciones)</span>";
            $entradas = $evento->getEntradasDisponibles();
            $info_entradas = $entradas > 0 ? "<p><strong>Entradas disponibles:</strong> $entradas</p>" : "<p class='agotado'>¡Agotado!</p>";
            
            
            $contenidoPrincipal .= <<<EOS
            <div class="evento-detalle">
                <img src="{$imagen}" alt="{$nombre}" class="evento-imagen-detalle">
                <div class="info-evento">
                    <h2>{$nombre} {$valoracionHTML}</h2>
                        <p><strong>Precio:</strong> {$precio} €</p>
                        <p><strong>Fecha:</strong> {$fecha}</p>
                        <p><strong>Ubicación:</strong> {$ubicacion}</p>
                        <p><strong>Organizador:</strong> {$organizador}</p>
                        {$info_entradas}
                        <div class="botones-evento">
                            {$botonCompra}
                            {$botonEditar}
                            {$botonEliminar}
                            {$botonForo}
                        </div>
                        <p><strong>Descripción:</strong> {$descripcion}</p>
                    </div>
                </div>
            EOS;

            // Mostrar valoraciones de los usuarios
            $contenidoPrincipal .= mostrarValoracionesEvento($id);

        } 
        else {
            $contenidoPrincipal .= "<p class='error'>Evento no encontrado</p>";
        }
    }

   
}


require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>
