<?php 
    session_start();
    $rol = $_SESSION["usuario_rol"];
?>
<!DOCTYPE html>
<html lang='es'>

<head>
    <meta charset="utf-8">
    <title>Perfil - Eventia</title>
    <link rel="stylesheet" type="text/css" href="CSS/estilo.css"/>
</head>
<body>
    <div id="contenedor">
        <?php require("includes/vistas/comun/cabecera.php"); ?>
        <?php require("includes/vistas/comun/sidebarIzq.php"); ?>

        <main>
            <!-- Datos -->
            
            <!-- Eventos -->
            <!-- Puntos -->
            <p><a href="logout.php">Cerrar sesión</a></p>
        </main>

        <?php require("includes/vistas/comun/sidebarDer.php"); ?>
        <?php require("includes/vistas/comun/pie.php"); ?>
    </div>
</body>
</html>
