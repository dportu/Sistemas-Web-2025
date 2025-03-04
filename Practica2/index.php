<?php 
session_start();

// Si el usuario no está autenticado, redirigir a la página de login
$usuarioAutenticado = isset($_SESSION["login"]) && $_SESSION["login"];

// Continuar con el resto del código para usuarios autenticados
?>
<!DOCTYPE html>
<html lang = 'es'>

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
        <?php include_once("eventos.php"); ?>
    </main>
    
    <?php require("includes/vistas/comun/sidebarDer.php"); ?>
    <?php require("includes/vistas/comun/pie.php"); ?>
    
    </div>
</body>
</html>
