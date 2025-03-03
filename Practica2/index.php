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
    <link id="estilo" rel="stylesheet" type="text/css" href="estilo.css"/>
</head>
<body>

    <div id="contenedor">

    <?php require("vistas/comun/cabecera.php"); ?>

    <?php require("vistas/comun/sidebarIzq.php"); ?>

    <main>
        <?php if(!$usuarioAutenticado): ?>
            <div class="error">
            <p>No se ha iniciado sesion. No tendras acceso a algunas funcionalidades</p>
            </div>
        <?php endif; ?>
        
        <h1>Eventia</h1>
        <p>Eventia es el portal de venta de entradas donde quieres estar.
            Ya seas un emergente organizador de eventos, una empresa con un amplio recorrido en este sector o
            un simple usuario más que desea disfrutar de eventos icónicos, Eventia ofrece todas aquellas funcionalidades indispensables para 
            la organización de dichos eventos, su anuncio, gestión y, el fácil y sencillo acceso a la compra-venta de entradas para los mismos.
            En esta página, además de ofrecer eventos iocónicos de artistas destacados en la industria, la variedad de organizadores implicados
            proporicona un amplio repertorio de eventos, heterogéneos entre sí, a disposición de los usuarios.
            Por otra parte, otra característica que hace que este portal destaque sobre otros existentes, es la posibilidad de una cómoda y directa
            comunicación entre clientes y organizadores. El foro, en el que los usuarios pueden valorar y dejar comentarios
            sobre los eventos a los que han asistido, o desean asistir, supone un gran nexo de unión en el que sus opiniones pueden ser
            escuchadas y atendidas.
        </p>
        <?php include_once("eventos.php"); ?>
        
    </main>
    
    <?php require("vistas/comun/sidebarDer.php"); ?>
    <?php require("vistas/comun/pie.php"); ?>
    
    </div>
</body>
</html>