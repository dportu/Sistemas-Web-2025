<!DOCTYPE html>
<html lang='es'>

<head>
    <meta charset="utf-8">
    <title>Foro Eventia - Nuevo Tema</title>
    <link id="estilo" rel="stylesheet" type="text/css" href="CSS/default.css"/>
</head>

<body>
    <p> Lista de enlaces: 
    <a href= "index.php">indice</a>,
        <a href= "foro.php">foro</a>, 
        <a href= "eventos.php">eventos</a>, 
        <a href= "miembros.html">miembros</a>, 
        <a href= "categorias.php">categorías</a>, 
    </p>

    <h1>Publicar Nuevo Mensaje en el Foro</h1>
    <form action="procesar_foro.php" method="post">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" required>
        <br><br>

        <label for="autor">Tu nombre:</label>
        <input type="text" id="autor" name="autor" required>
        <br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br><br>

        <label for="evento">Evento relacionado (opcional):</label>
        <select id="evento" name="evento">
            <option value="">Ninguno - Tema general</option>
            <?php
            // Incluir conexión a la base de datos
            include("conexion.php");
            
            // Consultar eventos disponibles
            $query = "SELECT id, nombre FROM eventos ORDER BY fecha_inicio DESC";
            $resultado = $conexion->query($query);
            
            // Generar opciones de eventos
            while($row = $resultado->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['nombre'] . "</option>";
            }
            
            $conexion->close();
            ?>
        </select>
        <br><br>

        <label for="mensaje">Mensaje:</label>
        <br>
        <textarea name="mensaje" id="mensaje" rows="10" cols="50" required></textarea>
        <br><br>

        <input type="checkbox" id="terminos" name="terminos" required>
        <label for="terminos">Acepto las normas del foro y que este mensaje puede ser visible para todos los usuarios.</label>
        <br><br>

        <input type="submit" value="Publicar mensaje">
    </form>
</body>
</html>