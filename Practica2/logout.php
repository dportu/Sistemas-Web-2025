<?php
session_start();


if (isset($_SESSION["login"])) {
    unset($_SESSION["login"]);
}
if (isset($_SESSION["nombre"])) {
    unset($_SESSION["nombre"]);
}
if (isset($_SESSION["esAdmin"])) {
    unset($_SESSION["esAdmin"]);
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

    <?php require("vistas/comun/cabecera.php"); ?>
    <?php require("vistas/comun/sidebarIzq.php"); ?>


    <main>
        <article>
            <h1>Sesión cerrada</h1>
            <p>Gracias por visitar nuestra web. Hasta pronto.</p>
            <p><a href="index.php">Volver a la página principal</a></p>
        </article>
    </main>

    <?php require("vistas/comun/sidebarDer.php"); ?>
    <?php require("vistas/comun/pie.php"); ?>

</div>

</body>
</html>