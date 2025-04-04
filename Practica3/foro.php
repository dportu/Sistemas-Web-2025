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
        $titulo = $mensajes[$i]->titulo;
        $autor = $mensajes[$i]->autor;
        $$id_evento = $mensajes[$i]->evento;
        $mensajeId = $mensajes[$i]->id;

        if (!$id_evento) {
            $nombre_evento = 'General';
        }
        else {
            $nombre_evento = Evento::buscaPorId($id_evento)->nombre;
        }
        $mensaje = $mensajes[$i]->mensaje;
        $fecha_publicacion = $mensajes[$i]->fechaPublicacion;

        $aplicacion = Aplicacion::getInstance();
        $modificarMensaje = '';

        $urlEdicion = "editar_mensaje.php";
        if ($mensajeId) {
            $urlEdicion = "editar_mensaje.php?id=$mensajeId";
        }
        $urlForo = 'foro.php';
        if ($id_evento) {
            $urlForo = "foro.php?id=$id_evento";
        }

        // Si el usuario está logueado y es el autor del mensaje, mostrar opciones de edición y eliminación
        if ($aplicacion->usuarioLogueado() && $aplicacion->nombreUsuario() === $autor) {
            $modificarMensaje .= "
                <a href='$urlEdicion' class='boton-enlace'>Editar</a> 
                <form action='$urlForo' method='POST' style='display:inline;'>
                    <input type='hidden' name='mensaje_id' value='$mensajeId'>
                    <button type='submit' name='accion' value='eliminar' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este mensaje?\")'>Eliminar</button>
                </form> 
                ";
        }

        // Eliminar mensaje
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? null;
        
            if ($accion) {
                mensajeForo::eliminarMensaje($mensajeId);
                if ($nombre_evento != 'General') {
                    header("Location: foro.php?id=$id_evento");
                } 
                else {
                    header("Location: foro.php");
                }
                exit;
            }
        }

		$contenidoPrincipal .= <<<EOS
            <div class='valoraciones'>
            <p class='mensaje-contenido'>
                <strong>Título:</strong> $titulo <br>
                <strong>Autor:</strong> $autor <br>
                <strong>Evento:</strong> $nombre_evento <br>
                <strong>Mensaje:</strong> $mensaje <br>
                <strong>Fecha:</strong> $fecha_publicacion <br> </p>
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