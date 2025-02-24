<?php 
include("conexion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="utf-8">
   <title>Foro de Eventos</title>
   <link rel="stylesheet" type="text/css" href="CSS/foro.css"/>
</head>

<body>
   <div id="foro-contenedor">
       <p class="enlaces">
           <a href="index.php">Inicio</a> |
           <a href="foro.php">Foro</a>
       </p>

       <h1>Foro de Eventos</h1>

       <div id="mensajes">
           <?php
           $resultado = $conexion->query("SELECT * FROM foro ORDER BY fecha_publicacion DESC");

           if ($resultado->num_rows > 0) {
               while ($fila = $resultado->fetch_assoc()) {
                   echo "<div class='mensaje'>";
                   echo "<strong>Título:</strong> " . htmlspecialchars($fila['titulo']) . "<br>";
                   echo "<strong>Autor:</strong> " . htmlspecialchars($fila['autor']) . "<br>";
                   echo "<strong>Email:</strong> " . htmlspecialchars($fila['email']) . "<br>";
                   echo "<strong>Evento:</strong> " . htmlspecialchars($fila['evento']) . "<br>";
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
           
           <label for="autor">Autor:</label>
           <input type="text" name="autor" id="autor" required><br><br>
           
           <label for="email">Email:</label>
           <input type="email" name="email" id="email" required><br><br>
           
           <label for="evento">Evento:</label>
           <input type="text" name="evento" id="evento" required><br><br>
           
           <label for="mensaje">Mensaje:</label>
           <textarea name="mensaje" id="mensaje" rows="4" required></textarea>

           <input type="submit" value="Enviar">
       </form>
   </div>

<?php
   $conexion->close();
?>
</body>
</html>