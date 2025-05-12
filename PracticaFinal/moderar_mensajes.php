<?php
    require_once __DIR__.'/includes/config.php';
    use es\ucm\fdi\aw\Aplicacion;
    use es\ucm\fdi\aw\foro\MensajeForo;
    use es\ucm\fdi\aw\usuarios\Usuario;
	use es\ucm\fdi\aw\eventos\Evento;

    $app = Aplicacion::getInstance();
    $tituloPagina = 'Moderación del Foro';
    $rutaApp = RUTA_APP;
    
    if ($app->tieneRol(Usuario::ADMIN_ROLE)) {
        // Obtener todos los mensajes del foro
        $mensajes = MensajeForo::getMensajes(null);
        
        $tablaMensajes = '';
        foreach ($mensajes as $mensaje) {
            $eventoNombre = $mensaje->getEvento() 
                ? Evento::buscaPorId($mensaje->getEvento())->getNombre() 
                : 'General';

            $tablaMensajes .= <<<EOS
            <tr>
                <td>{$mensaje->getTitulo()}</td>
                <td>{$mensaje->getAutor()}</td>
                <td>{$eventoNombre}</td>
                <td>{$mensaje->getFechaPublicacion()}</td>
                <td>
                    <a href="editar_mensajeForo.php?id={$mensaje->getId()}" class="boton-editar">Editar</a>
                    <form action="moderar_mensajes.php" method="POST"">
                        <input type="hidden" name="mensaje_id" value="{$mensaje->getId()}">
                        <button type="submit" name="accion" value="eliminar" 
                            class="boton-eliminar" 
                            onclick="return confirm('¿Eliminar este mensaje permanentemente?')">
                            Eliminar
                        </button>
                    </form>
                </td>
            </tr>
            EOS;
        }

        $contenidoPrincipal = <<<EOS
        <div class="admin-section">
            <h2>Moderación de Mensajes del Foro</h2>
            <table class="tabla-moderacion">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Evento</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    $tablaMensajes
                </tbody>
            </table>
        </div>
        EOS;

        // Procesar eliminación
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
            $mensajeId = $_POST['mensaje_id'] ?? null;
            if ($_POST['accion'] === 'eliminar' && $mensajeId) {
                try {
                    $mensaje = MensajeForo::getMensajePorId($mensajeId);
                    if ($mensaje){
                        $mensaje->eliminarMensaje($mensajeId);
                        header("Location: moderar_mensajes.php");
                        exit();
                    }
                    else {
                        throw new \Exception("Mensaje no encontrado.");
                    }
                } catch (\Exception $e) {
                    error_log("Error al eliminar el mensaje: " . $e->getMessage());
                    $contenidoPrincipal .= "<p class='error'>Error al eliminar el mensaje</p>";
                }
            }
        }
    } else {
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