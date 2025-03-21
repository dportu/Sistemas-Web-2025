<?php
session_start();

if (isset($_SESSION["login"])) {
    unset($_SESSION["login"]);
}
if (isset($_SESSION["usuario_nombre"])) {
    unset($_SESSION["usuario_nombre"]);
}
if (isset($_SESSION["usuario_email"])) {
    unset($_SESSION["usuario_email"]);
}
if (isset($_SESSION["usuario_rol"])) {
    unset($_SESSION["usuario_rol"]);
}


session_destroy();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" type="text/css" href="CSS/estilo.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Cierre de sesión</title>
</head>

<body>

<div id="contenedor"> 

    <?php require("includes/vistas/comun/cabecera.php"); ?>
    <?php require("includes/vistas/comun/sidebarIzq.php"); ?>


    <main>
        <article>
            <h1>Sesión cerrada</h1>
            <p>Gracias por visitar nuestra web. Hasta pronto.</p>
        </article>
    </main>

    <?php require("includes/vistas/comun/sidebarDer.php"); ?>
    <?php require("includes/vistas/comun/pie.php"); ?>

</div>

</body>
</html>