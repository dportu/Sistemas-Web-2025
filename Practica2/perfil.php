<?php
    session_start();
    require 'config.php'; // Cargar configuración de BD

    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        header("Location: login.php");
        exit();
    }

    $usuario_nombre = $_SESSION['usuario_nombre'];
    $usuario_email = $_SESSION['usuario_email'];
    $usuario_rol = $_SESSION['usuario_rol'];
   

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($usuario_nombre); ?></title>
    <link id="estilo" rel="stylesheet" type="text/css" href="CSS/estilo.css"/> <!-- Asegúrate de tener un archivo CSS -->
</head>
<body>
    <div id="contenedor">

    <?php require("includes/vistas/comun/cabecera.php"); ?>

    <?php require("includes/vistas/comun/sidebarIzq.php"); ?>
    <main>
    <h1>Bienvenido, <?php echo htmlspecialchars($usuario_nombre); ?>!</h1>
    <p>Email: <?php echo htmlspecialchars($usuario_email); ?></p>
    <a href="editar_perfil.php">Editar Perfil</a>
    
    <?php if ($usuario_rol === 'administrador' || $usuario_rol === 'promotor') { ?>
        <h2>Opciones de Administrador</h2>
        <ul>
            <p>Rol: <?php echo htmlspecialchars($usuario_rol); ?></p>
            <p> Aquí se mostrarán los eventos para poder gestionarlos.</p>
            <p><a href="anyadir_evento.php">Añadir evento</a></p>
        </ul>
    <?php } else { ?>
        <h2>Opciones de Usuario</h2>
        <ul>
            <a href="mis_compras.php">Mis Compras</a>
            <p> Aqui estarán los puntos del usuario. </p>
        </ul>
    <?php } ?>
    
    <a href="logout.php">Cerrar sesión</a>
    </main>
    <?php require("includes/vistas/comun/sidebarDer.php"); ?>
    <?php require("includes/vistas/comun/pie.php"); ?>
    
    </div>
</body>
</html>
