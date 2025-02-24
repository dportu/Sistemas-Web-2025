<?php
session_start();

// Si el usuario ya está autenticado, redirigir a la página principal
if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" type="text/css" href="CSS/default.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Login - Eventia</title>
</head>

<body class="login-page">

<div id="contenedor">
    <!--cabecera-->
    <?php require("vistas/comun/cabecera.php"); ?>
    
    <!--parte central con solo el formulario de login-->
    <main>
        <form action="procesarLogin.php" method="post">
            <fieldset>
                <legend>Acceso al sistema</legend>
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" id="usuario" required="required" />
                <br />
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required="required" />
                <br />
                <input type="submit" value="Entrar" />
            </fieldset>
            <?php
            // Mostrar errores de login si existen
            if (isset($_SESSION['error_login'])) {
                echo "<div class='error'>" . htmlspecialchars($_SESSION['error_login']) . "</div>";
                unset($_SESSION['error_login']); // limpiar el mensaje después de mostrar
            }
            ?>
        </form>
    </main>

    <?php require("vistas/comun/pie.php"); ?>
</div>

</body>
</html>