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

   // no la usamos por ahora eliminar si no
    function mostrarMensajes($conn, $parent_id = null, $nivel = 0) {
       
        if (is_null($parent_id)) {              // Aui depende de si es padre o no 
            $sql = "SELECT * FROM foro WHERE parent_id IS NULL ORDER BY fecha_publicacion ASC";
            $stmt = $conn->prepare($sql);
        } else {
            $sql = "SELECT * FROM foro WHERE parent_id = ? ORDER BY fecha_publicacion ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $parent_id);
        }
        $stmt->execute();
        $result = $stmt->get_result();
    
        
        while ($row = $result->fetch_assoc()) {
            // Esto hay que pasartlo a un css o algo
            echo '<div style="margin-left: ' . (20 * $nivel) . 'px; border-left: 1px solid #ccc; padding-left: 10px; margin-top: 10px;">';
            echo '<p><strong>' . htmlspecialchars($row['autor']) . '</strong> <small>(' . $row['fecha_publicacion'] . ')</small></p>';
            echo '<p>' . nl2br(htmlspecialchars($row['mensaje'])) . '</p>';
            
            echo '<p><a href="FormularioForo.php?responder_a=' . $row['id'] . '">Responder</a></p>';
           // Con esto llamamos recursivamente , cuando hay muchos se ve mal , hayque implementar un boton para ver respuestas
            mostrarMensajes($conn, $row['id'], $nivel + 1);
            echo '</div>';
        }
        $stmt->close();
    }
    

    function renderizarMensaje($mensaje, $aplicacion, $id_evento, $nivel = 0) {
       // Esto hay que pasartlo a un css o algo
        $margen = $nivel * 40;
        $borde = $nivel > 0 ? 'border-left: 3px solid #e0e0e0;' : '';
        
        $html = <<<EOS
        <div class="mensaje" style="margin-left: {$margen}px; $borde padding: 15px; margin-bottom: 15px; background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <div class="cabecera-mensaje">
                <h3 style="margin: 0; color: #2c3e50;">{$mensaje->getTitulo()}</h3>
                <div style="display: flex; gap: 10px; align-items: center; margin-top: 8px;">
                    <span style="color: #3498db;">@{$mensaje->getAutor()}</span>
                    <span style="color: #7f8c8d; font-size: 0.9em;">{$mensaje->getFechaPublicacion()}</span>
                </div>
            </div>
            
            <div class="cuerpo-mensaje" style="margin-top: 12px;">
                <p style="margin: 0; color: #34495e;">{$mensaje->getMensaje()}</p>
        EOS;
    
       // tambien meter a CSS
        if ($aplicacion->usuarioLogueado() && ($aplicacion->nombreUsuario() === $mensaje->getAutor() || $aplicacion->esAdmin())) {
            $html .= <<<EOS
                <div style="margin-top: 15px; display: flex; gap: 10px;">
                    <a href="{$aplicacion->buildUrl('editar_mensajeForo.php', ['id' => $mensaje->getId()])}" 
                       class="boton-editar" 
                       style="padding: 6px 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px;">
                        Editar
                    </a>

                    
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="mensaje_id" value="{$mensaje->getId()}">
                        <button type="submit" name="accion" value="eliminar" 
                                style="padding: 6px 12px; background: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer;">
                            Eliminar
                        </button>
                    </form>
                </div>
            EOS;
        }
    
        // responder nos funciona bien , pero hay que cambiarlo
        if ($aplicacion->usuarioLogueado()) {
            $form = new FormularioForo($id_evento, $mensaje->getId());
            $html .= <<<EOS
                <div style="margin-top: 15px;">
                    <button class="toggle-reply" 
                            style="padding: 8px 15px; background: #2ecc71; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Responder
                    </button>
                    <div class="formulario-respuesta" style="display: none; margin-top: 10px;">
                        {$form->gestiona()}
                    </div>
                </div>
            EOS;
        }
    
        // Con esto llamamos recursivamente , cuando hay muchos se ve mal , hayque implementar un boton para ver respuestas
        $respuestas = MensajeForo::getRespuestas($mensaje->getId());
        foreach ($respuestas as $respuesta) {
            $html .= renderizarMensaje($respuesta, $aplicacion, $id_evento, $nivel + 1);
        }
    
        $html .= "</div></div>";
        return $html; 
    }

    
    $id_evento = $_GET['id'] ?? null;
    $mensajesPrincipales = MensajeForo::getMensajes($id_evento);
    $parent_id = $_GET['parent_id'] ?? null;
    
    if (empty($mensajesPrincipales)) {
        $contenidoPrincipal .= "<p>Todavía no hay mensajes.</p>";
    } else {
       foreach ($mensajesPrincipales as $mensaje) {
       
        $contenidoPrincipal .= renderizarMensaje($mensaje, $aplicacion, $id_evento);
        }
    }

  
  // form para los mensajes nuevos 
    $form = new FormularioForo($id_evento);
    $htmlFormLogin = $form->gestiona();
    $contenidoPrincipal .= <<<EOS
        <div class="formulario-contenedor">
            $htmlFormLogin
        </div>
    EOS;

    // habria que pasar el script a un js?? 
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

