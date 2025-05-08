<?php 
    require_once __DIR__.'/includes/config.php';

    use es\ucm\fdi\aw\Aplicacion;
    use es\ucm\fdi\aw\eventos\Evento;
    use es\ucm\fdi\aw\foro\MensajeForo;
    use es\ucm\fdi\aw\foro\FormularioForo;

    $tituloPagina = 'Foro';
    $contenidoPrincipal = '';
    $aplicacion = Aplicacion::getInstance(); // <-- INICIALIZACIÓN AQUÍ
    $id_evento = $_GET['id'] ?? null;
	$mensajes = MensajeForo::getMensajes($id_evento);

    if (count($mensajes) == 0) {
        $contenidoPrincipal .= "<p>Todavía no hay mensajes.</p>";
    }

    function renderizarMensaje($mensaje, $aplicacion, $id_evento, $nivel = 0) {
        // Indentación progresiva
        $margen = $nivel * 50; // 50px por cada nivel de anidación
        
        $html = '<div class="mensaje" style="margin-left: '.$margen.'px; border-left: 2px solid #ddd; padding-left: 15px; margin-bottom: 20px;">';
        
        // Contenido principal del mensaje
        $html .= '<div class="contenido-mensaje">';
        $html .= sprintf('
            <p class="mensaje-contenido">
                <strong>Título:</strong> %s <br>
                <strong>Autor:</strong> %s <br>
                <strong>Mensaje:</strong> %s <br>
                <strong>Fecha:</strong> %s
            </p>',
            htmlspecialchars($mensaje->getTitulo()),
            htmlspecialchars($mensaje->getAutor()),
            nl2br(htmlspecialchars($mensaje->getMensaje())),
            $mensaje->getFechaPublicacion()
        );
        
        // Botones de acciones
        if ($aplicacion->usuarioLogueado() && ($aplicacion->nombreUsuario() === $mensaje->getAutor() || $aplicacion->esAdmin())) {
            $html .= sprintf('
                <div class="acciones-mensaje">
                    <a href="%s" class="boton-enlace">Editar</a>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="mensaje_id" value="%d">
                        <button type="submit" name="accion" value="eliminar">Eliminar</button>
                    </form>
                </div>',
                $aplicacion->buildUrl('editar_mensajeForo.php', ['id' => $mensaje->getId()]),
                $mensaje->getId()
            );
        }
        
        // Botón de responder
        if ($aplicacion->usuarioLogueado()) {
            $form = new FormularioForo($id_evento, $mensaje->getId());
            $html .= '<button class="toggle-reply">Responder</button>';
            $html .= '<div class="formulario-respuesta" style="display:none;">'.$form->gestiona().'</div>';
        }
        
        $html .= '</div>'; // Cierre contenido-mensaje
        
        // Respuestas (llamada recursiva)
        $respuestas = MensajeForo::getRespuestas($mensaje->getId());
        foreach ($respuestas as $respuesta) {
            $html .= renderizarMensaje($respuesta, $aplicacion, $id_evento, $nivel + 1);
        }
        
        return $html.'</div>'; // Cierre div.mensaje
    }

    
    $id_evento = $_GET['id'] ?? null;
    $mensajesPrincipales = MensajeForo::getMensajes($id_evento);
    
    if (empty($mensajesPrincipales)) {
        $contenidoPrincipal .= "<p>Todavía no hay mensajes.</p>";
    } else {
       foreach ($mensajesPrincipales as $mensaje) {
        $contenidoPrincipal .= renderizarMensaje($mensaje, $aplicacion, $id_evento);
        }
    }

     // Formulario para añadir un nuevo mensaje al foro

    $form = new FormularioForo($id_evento);
    $htmlFormLogin = $form->gestiona();
    $contenidoPrincipal .= <<<EOS
        <div class="formulario-contenedor">
            $htmlFormLogin
        </div>
    EOS;

    $contenidoPrincipal .= <<<EOS
    <script>
    document.querySelectorAll('.toggle-reply').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.nextElementSibling;
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });
    });
    </script>
    EOS;

	require __DIR__.'/includes/vistas/plantillas/plantilla.php';
  

    
    /*
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
	}*/

?>

