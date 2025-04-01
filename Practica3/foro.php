<?php 

	require_once __DIR__.'/includes/config.php';

    use es\ucm\fdi\aw\Aplicacion;
    use es\ucm\fdi\aw\eventos\Evento;
	use es\ucm\fdi\aw\foro\mensajeForo;
    use es\ucm\fdi\aw\foro\FormularioForo;

	$tituloPagina = 'Foro';

	$contenidoPrincipal = '';

    // Mostrar el foro dependiendo de su categoría
    $id_evento = $_GET['id'] ?? null;
	$mensajes = mensajeForo::getMensajes($id_evento);

    if (count($mensajes) == 0) {
        $contenidoPrincipal .= "<p>Todavía no hay mensajes.</p>";
    }

	for ($i = 0; $i < count($mensajes); $i++) {
        $titulo = $mensajes[$i]->getTitulo();
        $autor = $mensajes[$i]->getAutor();
        $nombre_evento = $mensajes[$i]->getEvento();
        if (!$nombre_evento) {
            $nombre_evento = 'General';
        }
        else {
            $nombre_evento = Evento::buscaPorId($nombre_evento)->getNombre();
        }
        $mensaje = $mensajes[$i]->mensaje;
        $fecha_publicacion = $mensajes[$i]->fechaPublicacion;

        $aplicacion = Aplicacion::getInstance();
        $modificarMensaje = '';
        // Si el usuario está logueado y es el autor del mensaje, mostrar opciones de edición y eliminación
        if ($aplicacion->usuarioLogueado() && $aplicacion->nombreUsuario() === $autor) {
            $modificarMensaje .= 
                "<form action='' method='POST' style='display:inline;'>
                    <input type='hidden' name='mensaje_id' value='$mensajeId'>
                    <button type='submit' name='accion' value='eliminar'>Eliminar</button>
                </form>
                <form action='' method='POST' style='display:inline;'>
                    <input type='hidden' name='mensaje_id' value='$mensajeId'>
                    <button type='submit' name='accion' value='editar'>Editar</button>
                </form>";
        }

		$contenidoPrincipal .= <<<EOS
            <div class='mensaje'>
                <strong>Título:</strong> $titulo <br>
                <strong>Autor:</strong> $autor <br>
                <strong>Evento:</strong> $nombre_evento <br>
                <p class='mensaje-contenido'>$mensaje</p>
                <small><strong>Fecha:</strong> $fecha_publicacion</small>
                $modificarMensaje
            </div>
        EOS;
	}

    // Formulario para añadir un nuevo mensaje al foro

    $form = new FormularioForo($id_evento);
    $htmlFormLogin = $form->gestiona();
    $contenidoPrincipal .= <<<EOS
        <div class="formulario-contenedor">
            $htmlFormLogin
        </div>
    EOS;

	require __DIR__.'/includes/vistas/plantillas/plantilla.php';
  
?>