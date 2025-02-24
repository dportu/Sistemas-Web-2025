<?php
session_start();

$usuarios_validos = [
    "user" => [
        "password" => "userpass",
        "nombre" => "Usuario",
    ],
    "admin" => [
        "password" => "adminpass",
        "nombre" => "Administrador",
        "esAdmin" => true
    ]
];


if ($_SERVER["REQUEST_METHOD"] == "POST") { // comprueba si se recibieron datos por POST y obtiene el usuario y contraseña 
    $usuario = $_POST["usuario"];  
    $password = $_POST["password"];

    // compureba q las credenciales son correctas y dependiendo elige va una de las 2 plantillas
    if ( isset($usuarios_validos[$usuario]) && $password === $usuarios_validos[$usuario]["password"] ) {
     // credenciales correctas
        $_SESSION["login"] = true;
        $_SESSION["nombre"] = $usuarios_validos[$usuario]["nombre"];

        //comprueba si es admin
        if (isset($usuarios_validos[$usuario]["esAdmin"])) {
            $_SESSION["esAdmin"] = true;
        }
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <link rel="stylesheet" type="text/css" href="CSS/default.css" />
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <title>Login procesado</title>
        </head>

        <body>
            <div id="contenedor">

                <?php require("vistas/comun/cabecera.php"); ?>
                <?php require("vistas/comun/sidebarIzq.php"); ?>

                <main>
                    <article>
                        <h1>Bienvenido <?php echo htmlspecialchars($_SESSION["nombre"]); ?></h1>
                        <p>Has iniciado sesión correctamente.</p>
                        <p>Usa el menú de la izquierda para navegar.</p>
                    </article>
                </main>

                <?php require("vistas/comun/sidebarDer.php"); ?>
                <?php require("vistas/comun/pie.php"); ?>

            </div> 
        </body>
        </html>
        <?php
    } else {
     // credenciales incorrectas
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <link rel="stylesheet" type="text/css" href="CSS/estilo.css" />
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <title>Error de login</title>
        </head>

        <body>

        <div id="contenedor">

            <?php require("vistas/comun/cabecera.php"); ?>
            <?php require("vistas/comun/sidebarIzq.php"); ?>

            
            <main>
                <article>
                    <h1>ERROR</h1>
                    <p>El usuario o contraseña no son válidos.</p>
                    <p><a href="login.php">Volver a intentarlo</a></p>
                </article>
            </main>

            <?php require("vistas/comun/sidebarDer.php"); ?>
            <?php require("vistas/comun/pie.php"); ?>

        </div> 

        </body>
        </html>
        <?php
    }
}
?>