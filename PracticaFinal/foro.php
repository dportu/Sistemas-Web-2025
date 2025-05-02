<?php 
	require_once __DIR__.'/includes/config.php';

    use es\ucm\fdi\aw\Aplicacion;
    use es\ucm\fdi\aw\eventos\Evento;
	use es\ucm\fdi\aw\foro\MensajeForo;
    use es\ucm\fdi\aw\foro\FormularioForo;

	$tituloPagina = 'Foro';

	$contenidoPrincipal = '';

    // Mostrar el foro dependiendo de su categoría
    $id_evento = $_GET['id'] ?? null;
	$mensajes = MensajeForo::getMensajes($id_evento);

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
        #$urlEdicion = $aplicacion->buildUrl('editar_mensaje.php');
        
        if ($mensajeId) {
            $params = ['id' => $mensajeId];
            $urlEdicion = $aplicacion->buildUrl('editar_mensajeForo.php', $params);
            
        }
        $urlForo = $aplicacion->buildUrl('foro.php');
        

        if ($id_evento) {
            $params = ['id' => $id_evento];
            $urlForo = $aplicacion->buildUrl('foro.php', $params);
        }

        // Si el usuario está logueado y es el autor del mensaje, mostrar opciones de edición y eliminación
        if ($aplicacion->usuarioLogueado() && ($aplicacion->nombreUsuario() === $autor || $aplicacion->esAdmin())) { // || que sea promotor del evento
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
                MensajeForo::eliminarMensaje($mensajeId);
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