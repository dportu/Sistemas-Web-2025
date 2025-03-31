<?php 

	require_once __DIR__.'/includes/config.php';

	use es\ucm\fdi\aw\foro\mensajeForo;

	$tituloPagina = 'Foro';

	$contenidoPrincipal = '';

    // TODO: Mostrar el foro dependiendo de su categoría
	// TODO: Comprobar que funciona la clase foro
	$mensajes = mensajeForo::getMensajes($_GET['id'] ?? null);
	for ($i = 0; $i < count($mensajes); $i++) {
		$contenidoPrincipal .= <<<EOS
			
		EOS;
	}

	/*
		if ($resultado->num_rows > 0) {
                        while ($fila = $resultado->fetch_assoc()) {
                            echo "<div class='mensaje'>";
                            echo "<strong>Título:</strong> " . htmlspecialchars($fila['titulo']) . "<br>";
                            echo "<strong>Autor:</strong> " . htmlspecialchars($fila['autor']) . "<br>";
                            echo "<strong>Evento:</strong> " . htmlspecialchars($fila['nombre_evento'] ? $fila['nombre_evento'] : 'General') . "<br>";
                            echo "<p class='mensaje-contenido'>" . nl2br(htmlspecialchars($fila['mensaje'])) . "</p>";
                            echo "<small><strong>Fecha:</strong> " . $fila['fecha_publicacion'] . "</small>";
                            echo "</div>";

                            if ($usuarioLogueado && $_SESSION['usuario_nombre'] === $fila['autor']) {
                                echo "<div class='acciones-mensaje'>";
                                echo "<a href='editar_mensaje.php?id=" . $fila['id'] . "'>Editar     </a>";
                                echo "<a href='eliminar_mensaje.php?id=" . $fila['id'] . "' class='eliminar' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este mensaje?\")'>Eliminar</a>";
                                echo "</div>";
                            }
                        }
                    } 
            else {
                echo "<p>No hay mensajes aún.</p>";
            }

			// Formulario para enviar un nuevo mensaje al foro (Ver foro.php de la practica 2)
	*/

	require __DIR__.'/includes/vistas/plantillas/plantilla.php';
  
?>