<?php 
    include("conexion_bd.php");
    session_start(); // Manejo de sesiones para el usuario

    $_SESSION['usuario_nombre'] = $_SESSION['usuario_nombre'] ?? '';
    $_SESSION['usuario_email'] = $_SESSION['usuario_email'] ?? '';

    $usuarioLogueado = isset($_SESSION["login"]) && $_SESSION["login"] === true;
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
           $resultado = $conexion->query("SELECT f.*, e.nombre AS nombre_evento FROM foro f LEFT JOIN eventos e ON f.evento = e.id ORDER BY fecha_publicacion DESC");

           if ($resultado->num_rows > 0) {
               while ($fila = $resultado->fetch_assoc()) {
                   echo "<div class='mensaje'>";
                   echo "<strong>Título:</strong> " . htmlspecialchars($fila['titulo']) . "<br>";
                   echo "<strong>Autor:</strong> " . htmlspecialchars($fila['autor']) . "<br>";
                   echo "<strong>Email:</strong> " . htmlspecialchars($fila['email']) . "<br>";
                   echo "<strong>Evento:</strong> " . htmlspecialchars($fila['nombre_evento'] ? $fila['nombre_evento'] : 'Ninguno') . "<br>";
                   echo "<p class='mensaje-contenido'>" . nl2br(htmlspecialchars($fila['mensaje'])) . "</p>";
                   echo "<small><strong>Fecha:</strong> " . $fila['fecha_publicacion'] . "</small>";
                   echo "</div>";
               }
           } else {
               echo "<p>No hay mensajes aún.</p>";
           }
           ?>
       </div>

       <form id="nuevo-mensaje" action="procesar_foro.php" method="post">
           <h2>Escribe un mensaje</h2>
           
           <label for="titulo">Título:</label>
           <input type="text" name="titulo" id="titulo" required><br><br>
           
           <!-- Campos ocultos para autor y email -->
           <input type="hidden" name="autor" value="<?= htmlspecialchars($_SESSION['usuario_nombre']) ?>">
           <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['usuario_email']) ?>">
           
           <!-- Mostrar información del usuario (opcional) -->
           <p>Publicando como: <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong></p>
           <p>Publicando como: <strong><?php echo htmlspecialchars($_SESSION['usuario_email']); ?></strong></p>
           
           <label for="evento">Evento:</label>
           <select id="evento" name="evento">
               <option value="">Ninguno - Tema general</option>
               <?php
               // Consultar eventos disponibles
               $query = "SELECT id, nombre FROM eventos ORDER BY fecha_inicio DESC";
               $resultado_eventos = $conexion->query($query);
               
               // Generar opciones de eventos
               while($row = $resultado_eventos->fetch_assoc()) {
                   echo "<option value='" . $row['id'] . "'>" . $row['nombre'] . "</option>";
               }
               ?>
           </select><br><br>
           
           <label for="mensaje">Mensaje:</label>
           <textarea name="mensaje" id="mensaje" rows="4" required></textarea>

           <input type="submit" value="Enviar">
       </form>
   </div>

    </main>

    <?php require("includes/vistas/comun/sidebarDer.php"); ?>
    <?php require("includes/vistas/comun/pie.php"); ?>
   
   <?php $conexion->close(); ?>

</body>


</html>