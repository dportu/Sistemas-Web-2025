<?php
    session_start();
?>


<!DOCTYPE html>
<html lang = 'es'>

<head>
    <meta charset = "utf-8">
    <title>Eventia: Detalles</title>
    <link id="estilo" rel="stylesheet" type="text/css" href="CSS/default.css"/>
</head>
 <body>
 <div id="contenedor">

    <?php require("vistas/comun/cabecera.php"); ?>

    <?php require("vistas/comun/sidebarIzq.php"); ?>
    <h1>Introducción</h1>
        <p>
            Eventia tiene como objetivo simplificar el proceso de encontrar planes de alta calidad para todo tipo de usuario.<br>
            Tanto si lo que buscas es un concierto de heavy metal o un evento de networking donde encontrar tu próximo empleo, Eventia tiene lugar para todo ello.<br>
            Lo que nos diferencia de otros portales de eventos es nuestra simplicidad, cercanía y la posibilidad de interacción y comunicación directa entre empresas y usuarios, es como si se tratara de una red social!
        </p>
        <!--poner logotipo-->
    <h2>Funcionalidades</h2>
        <p>
            <strong>Eventos</strong>: gestión de los eventos publicados en la página web. Se podrán añadir, eliminar y modificar todo tipo de eventos.<br>
            <strong>Administrador</strong>: los administradores podrán acceder a todas las funcionalidades, pudiendo gestionar fácilmente todos los aspectos de la página.<br>
            <strong>Foro</strong>: en el foro, los usuarios registrados podrán publicar comentarios, dudas y conectar con otros usuarios.<br>
            <strong>Valoraciones</strong>: los usuarios registrados podrán además valorar los eventos a los que hayan asistido.<br>
            <strong>Sistemas de puntos</strong>: los usuarios generarán puntos con cada compra, que podrán usar más tarde para obtener descuentos.<br>
        </p>
    <h3>Usuarios</h3>
        <p>
            <strong>Administrador</strong>: Tendrá acceso a todos los privilegios de la Web y todas las funcionalidades, podrán gestionar el contenido y modificar la web.<br>
            <strong>Usuario sin registrar</strong>: Tiene acceso a visualizar los eventos disponibles, sin opción de comprar ni participar en el foro.<br>
            <strong>Usuario Registrado</strong>: Tiene acceso a todos los eventos, a compras de entradas, a promociones, a utilización del foro y de las valoraciones tras la compra de una entrada.<br>
            <strong>Promotor</strong>: Tiene capacidad de añadir eventos, de emitir promociones y modificar fechas de sus eventos.<br>
            <strong>Usuario Premium</strong>: Tiene todas las capacidades de los demás usuarios y con un pago mensual tiene acceso a privilegios como descuentos, entradas exclusivas y aumento de puntos.<br>
        </p>

    <?php require("vistas/comun/sidebarDer.php"); ?>
    <?php require("vistas/comun/pie.php"); ?>
    </div>
</body>

 </html>
