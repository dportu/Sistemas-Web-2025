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
        <!-- TODO: Modificar Practica 2 prueba -->
    </main>

    <?php require("includes/vistas/comun/sidebarDer.php"); ?>
    <?php require("includes/vistas/comun/pie.php"); ?>
   <div id="foro-contenedor">

   
</body>
</html>