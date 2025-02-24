<?php 
include("conexion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Foro de Correos</title>
    <link rel="stylesheet" type="text/css" href="CSS/foro.css"/>
</head>

<body>
    <div id="foro-contenedor">
        <p class="enlaces">
            <a href="index.php">Inicio</a> |
            <a href="contacto.php">Contacto</a>
        </p>

        <h1>Foro de Correos</h1>

        <div id="mensajes">
            <?php
            $resultado = $conexion->query("SELECT * FROM correos ORDER BY fecha DESC");

            if ($resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
                    echo "<div class='mensaje'>";
                    echo "<strong>Nombre:</strong> " . htmlspecialchars($fila['nombre']) . "<br>";
                    echo "<strong>Email:</strong> " . htmlspecialchars($fila['email']) . "<br>";
                    echo "<strong>Motivo:</strong> " . htmlspecialchars($fila['motivo']) . "<br>";
                    echo "<p class='consulta'>" . nl2br(htmlspecialchars($fila['consulta'])) . "</p>";
                    echo "<small><strong>Fecha:</strong> " . $fila['fecha'] . "</small>";
                    echo "</div>";
                }
            } else {
                echo "<p>No hay mensajes aún.</p>";
            }
            ?>
        </div>

        <form id="nuevo-mensaje" action="procesar_contacto.php" method="post">
            <h2>Escribe un mensaje</h2>
            <label for="mensaje">Mensaje:</label>
            <textarea name="mensaje" id="mensaje" rows="4" required></textarea>

            <label for="motivo">Motivo:</label>
            <select name="motivo" id="motivo" required>
                <option value="consulta">Consulta</option>
                <option value="sugerencia">Sugerencia</option>
                <option value="queja">Queja</option>
            </select>

            <input type="submit" value="Enviar">
        </form>
    </div>

<?php
    $conexion->close();
?>
</body>
</html>
