<?php
    include("info_evento.php");

    session_start();

    // Si el usuario no está autenticado, redirigir a la página de login
    $usuarioAutenticado = isset($_SESSION["login"]) && $_SESSION["login"];
    $id_evento = $_GET['id'];

    function mostrarInfoEvento() {
        global $id_evento;
        $evento = getEvento($id_evento);

        echo "<h2>[ " . htmlspecialchars($evento['nombre']) . " ]</h2>";

        echo "<img src='" . htmlspecialchars($evento['imagen']) . "' alt='Imagen del evento'>";
        echo "<p><strong>Precio:</strong> " . $evento['precio'] . " €</p>";
        echo "<p><strong>Fecha:</strong> " . $evento['fecha_inicio'] . "</p>";

        echo !empty($evento['ubicacion']) ? "<p><strong>Ubicacion:</strong> " . htmlspecialchars($evento['ubicacion']) . "</p>" : "";
        echo !empty($evento['organizador']) ? "<p><strong>Organizador:</strong> " . htmlspecialchars($evento['organizador']) . "</p>" : "";
        echo !empty($evento['descripcion']) ? "<p><strong>Descripción:</strong> " . htmlspecialchars($evento['descripcion']) . "</p>" : "";
    }
?>
<?php


// Continuar con el resto del código para usuarios autenticados
?>
<!DOCTYPE html>
<html lang = 'es'>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Eventia</title>
    <link id="estilo" rel="stylesheet" type="text/css" href="CSS/estilo.css">
</head>
<body>

    <div id="contenedor">

    <?php require("includes/vistas/comun/cabecera.php"); ?>

    <?php require("includes/vistas/comun/sidebarIzq.php"); ?>

    <main>
        <?php 
            mostrarInfoEvento();
            if ($usuarioAutenticado && $_SESSION["usuario_rol"] == "administrador") { 
        ?>

        <!-- Elegir entre (formulario y funciones en un mismo script) o (ir a un script diferente con cada botón) -->
        <!-- Lo que verá el admin o promotor de ese evento -->
        <a href="editar_evento.php" class="boton_editar">Editar</a>
       
        <a href="eliminar_evento.php" class="boton_eliminar">Eliminar</a>
      

        <?php 
            } else {
        ?>

        <!-- Lo que verá el usuario normal -->
        <a href="compra.php?id=<?php echo $id_evento; ?>" class="boton-compra">Compra</a>


        <?php 
            }
        ?>

        <!-- Esto lo verán todos -->
        <a href="valoraciones.php?id=<?php echo $id_evento; ?>"  class="boton-valoraciones">Valoraciones</a>
     

        <a href="foro.php?id=<?php echo $id_evento;?>" class= "boton-foro">Foro evento</a>
      
    </main>
    
    <?php require("includes/vistas/comun/sidebarDer.php"); ?>
    <?php require("includes/vistas/comun/pie.php"); ?>
    
    </div>
</body>
</html>