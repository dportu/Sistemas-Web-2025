<?php
require_once __DIR__.'/includes/config.php';
use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\eventos\Evento;
use es\ucm\fdi\aw\usuarios\Usuario;

$app = Aplicacion::getInstance();
$tituloPagina = 'Moderación de Eventos';
$usuario = Usuario::buscaUsuario($app->nombreUsuario());
$esAdmin = $app->tieneRol(Usuario::ADMIN_ROLE);
$esPromotor = $app->tieneRol(Usuario::PROMOTOR_ROLE);
$usuarioNombre = $usuario->getUsername(); // Obtener ID del usuario actual

if ($esAdmin ){

    if($esAdmin) {
        $eventos = Evento::getEventos();

    }

    $tablaEventos = '';
    foreach ($eventos as $evento) {
        $eventoId = $evento->getId();
        $tablaEventos .= <<<EOS
        <tr>
            <td>{$evento->getNombre()}</td>
            <td>{$evento->getPrecio()} €</td>
            <td>{$evento->getFecha()}</td>
            <td>{$evento->getUbicacion()}</td>
            <td>
                <a href="editar_evento.php?id={$eventoId}" class="boton-editar">Editar</a>
                <form action="moderar_eventos.php" method="POST">
                    <input type="hidden" name="evento_id" value="{$eventoId}">
                    <button type="submit" name="accion" value="eliminar" 
                        class="boton-eliminar" 
                        onclick="return confirm('¿Eliminar este evento permanentemente?')">
                        Eliminar
                    </button>
                </form>
            </td>
        </tr>
        EOS;
    }


    $contenidoPrincipal = <<<EOS
    <div class="admin-section">
        <h2>Gestión de Eventos</h2>
        <a href="anyadir_evento.php" class="boton-crear">Añadir Nuevo Evento</a>
        <table class="tabla-moderacion">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Fecha</th>
                    <th>Ubicación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                $tablaEventos
            </tbody>
        </table>
    </div>
    EOS;

    // Procesar eliminación
    // En la sección de procesamiento POST:
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
        $eventoId = filter_input(INPUT_POST, 'evento_id', FILTER_VALIDATE_INT);
        
        if ($_POST['accion'] === 'eliminar' && $eventoId) {
            try {
                $evento = Evento::buscaPorId($eventoId);
                if ($evento) {
                    $evento->eliminarEvento();
                    header("Location: moderar_eventos.php");
                    exit();
                } else {
                    throw new \Exception("Evento no encontrado");
                }
            } catch (\Exception $e) {
                error_log("Error al eliminar evento: " . $e->getMessage());
                $contenidoPrincipal .= "<p class='error'>Error al eliminar el evento</p>";
            }
        }
    }
    } 
    else if ($esPromotor) {
        $contenidoPrincipal = <<<EOS
    <div class="admin-section">
        <h2>Gestión de Eventos</h2>
        <a href="anyadir_evento.php" class="boton-crear">Añadir Nuevo Evento</a>

    </div>
    EOS;
    }
    else {
        $contenidoPrincipal = <<<EOS
        <div class="acceso-denegado">
            <h2>Acceso Denegado!</h2>
            <p>No tienes permisos de administrador.</p>
            <a href="index.php" class="boton-volver">Volver al Inicio</a>
        </div>
        EOS;
    }

    require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>