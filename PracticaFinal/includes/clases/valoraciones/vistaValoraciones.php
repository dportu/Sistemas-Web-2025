<?php
    use es\ucm\fdi\aw\valoraciones\Valoracion;
    use es\ucm\fdi\aw\Aplicacion;

    function modificarValoracion($app, $valoracion, $id_evento) {
        $acciones = '';
        if ($app->usuarioLogueado() && $app->nombreUsuario() === $valoracion->getUsername()) {
            // Si es el autor o administrador, puede editar/eliminar
            $urlEditar = $app->buildUrl('editar_valoracion.php', ['id' => $valoracion->getId()]);
            $urlActual = $app->buildUrl('vistaValoraciones.php', ['id' => $id_evento]);

            $acciones .= <<<EOS
            <a href="$urlEditar" class="boton-enlace">Editar</a>
            <form action="$urlActual" method="POST" style="display:inline;">
                <input type="hidden" name="valoracion_id" value="{$valoracion->getId()}">
                <button type="submit" name="accion" value="eliminar" onclick="return confirm('¿Eliminar esta valoración?')">Eliminar</button>
            </form>
            EOS;
        }
        return $acciones;
    }
    
    function mostrarValoracionesEvento($evento): string {
        $html = "<div class='valoraciones'><h3>Valoraciones de los usuarios:</h3>";

        $valoraciones = Valoracion::valoracionesEvento($evento);
        $app = Aplicacion::getInstance();

        if (empty($valoraciones)) {
            $html .= "<p>No hay valoraciones todavía.</p>";
        } else {
            foreach ($valoraciones as $valoracion) {
                $usuarioNombre = htmlspecialchars($valoracion->getUsername());
                $puntuacion = htmlspecialchars($valoracion->getNota());
                $comentario = $valoracion->getComentario();
                $fecha = htmlspecialchars($valoracion->getFecha());

                $comentarioHTML = !empty($comentario) ? "<p><strong>Comentario:</strong> ".htmlspecialchars($comentario)."</p>" : '';
                
                $modificarValoracion = '';
                // $modificarValoracion = modificarValoracion($app, $valoracion, $evento->getId());

                $html .= <<<EOS
                    <div class="valoracion">
                        <p><strong>Usuario:</strong> $usuarioNombre</p>
                        <p><strong>Puntuación:</strong> $puntuacion/5</p>
                        $comentarioHTML
                        <p><strong>Fecha:</strong> $fecha</p>
                        $modificarValoracion
                    </div>
                EOS;
            }
        }

        // Eliminar valoración (si se envió por POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
            $valoracionId = $_POST['valoracion_id'] ?? null;

            if ($valoracionId && $app->usuarioLogueado()) {
                $valoracion = Valoracion::getValoracionPorId($valoracionId);
                if ($valoracion && ($valoracion->getUsername() === $app->nombreUsuario() || $app->esAdmin())) {
                    Valoracion::eliminarValoracion($valoracionId);
                    header("Location: vistaEvento.php?id=$evento");
                    exit;
                }
            }
        }

        $html .= "</div>"; // Cierre de .valoraciones
        return $html;
    }
?>