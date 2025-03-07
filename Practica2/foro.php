    <?php 
        include("conexion_bd.php");
        include("info_evento.php");

        session_start(); 

        $_SESSION['usuario_nombre'] = $_SESSION['usuario_nombre'] ?? '';
        $_SESSION['usuario_email'] = $_SESSION['usuario_email'] ?? '';

        $usuarioLogueado = isset($_SESSION["login"]) && $_SESSION["login"] === true;

        $id_evento = isset($_GET['id']) ? intval($_GET['id']) : null;
        if ($id_evento != null) {

            $evento = getEvento($id_evento);
            $nombre_evento = $evento['nombre'];
        }
        else {
            $nombre_evento = 'General';
        }
    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Eventia</title>
        <link id="estilo" rel="stylesheet" type="text/css" href="CSS/estilo.css"/>

    </head>

    <body>
        <div id="contenedor">

        <?php require("includes/vistas/comun/cabecera.php"); ?>

        <?php require("includes/vistas/comun/sidebarIzq.php"); ?>

        <main>
            <h1>Foro de Eventos</h1>

        <div id="mensajes">
                <?php
                    // Consulta correcta con JOIN entre foro y eventos
                    $sql = "SELECT f.*, e.nombre AS nombre_evento 
                            FROM foro f 
                            LEFT JOIN eventos e ON f.evento = e.id ";
                    
                    if ($id_evento != null) {
                        $sql .= "WHERE f.evento = $id_evento ";
                    }

                    $sql .= "ORDER BY fecha_publicacion DESC";

                    $resultado = $conexion->query($sql);

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
            ?>
        </div>
            
        <?php
                if (!$usuarioLogueado) {
                    echo "<p><a href='login.php'>Inicia sesión</a> para poder publicar un mensaje.</p>";
                } else {
            ?>
            <form id="nuevo-mensaje" action="procesar_foro.php" method="post"> 
                <h2>Escribe un mensaje</h2>

                <label for="titulo">Título:</label>
                <input type="text" name="titulo" id="titulo" required><br><br>

                <!-- Esto es para ocultar el autor y el email-->
                <input type="hidden" name="autor" value="<?= htmlspecialchars($_SESSION['usuario_nombre']) ?>">
                <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['usuario_email']) ?>">

                <!-- Campo oculto para el ID del evento -->
                <input type="hidden" name="evento" value="<?= htmlspecialchars($id_evento) ?>">

                <p>Publicando como: <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong></p>
                <p>Evento: <strong><?php echo htmlspecialchars($nombre_evento); ?></strong></p>

                <label for="mensaje">Mensaje:</label>
                <textarea name="mensaje" id="mensaje" rows="4" required></textarea>

                <input type="submit" value="Enviar">
            </form>
        <?php } ?>
    

        </main>

        <?php require("includes/vistas/comun/sidebarDer.php"); ?>
        <?php require("includes/vistas/comun/pie.php"); ?>
    
    <?php $conexion->close(); ?>

    </body>


    </html>