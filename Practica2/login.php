<?php
session_start();

/*if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
    header("Location: index.php");
    exit;
}*/

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" type="text/css" href="CSS/estilo.css"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Login - Eventia</title>
</head>
<body class="login-page">

    <div id="contenedor">

    <?php require("includes/vistas/comun/cabecera.php"); ?>
    
    <main>
        <form action="procesarLogin.php" method="post">
            <fieldset>
                <legend>Acceso al sistema</legend>
                <label for="usuario">Nombre de usuario:</label>
                <input type="text" name="usuario" id="usuario" required>
                <br>
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required>
                <br>
                <input type="submit" value="Entrar">
            </fieldset>
            <?php
            if (isset($_SESSION['error_login'])) {
                echo "<div class='error'>" . htmlspecialchars($_SESSION['error_login']) . "</div>";
                unset($_SESSION['error_login']);
            }
            ?>
        </form>
        <a href="registro.php">Registrarse</a>
    </main>

    <?php require("includes/vistas/comun/pie.php"); ?>
</div>
</body>
</html>