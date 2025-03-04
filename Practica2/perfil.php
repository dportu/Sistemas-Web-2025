<?php 
    session_start();
    $rol = $_SESSION["usuario_rol"];
?>
<!DOCTYPE html>
<html lang='es'>

<head>
    <meta charset="utf-8">
    <title>Perfil - Eventia</title>
    <link rel="stylesheet" type="text/css" href="CSS/estilo.css"/>
</head>
<body>
    <div id="contenedor">
        <?php require("includes/vistas/comun/cabecera.php"); ?>
        <?php require("includes/vistas/comun/sidebarIzq.php"); ?>

        <main>
            <h2>Bienvenid@, <?= htmlspecialchars($_SESSION["usuario_nombre"]); ?></h2>
            <p>Tu correo: <?= htmlspecialchars($_SESSION["usuario_email"]); ?></p>

            <?php if ($rol === "admin"): ?>
                <h3>Panel de Administrador</h3>
                <ul>
                    <li><a href="gestion_eventos.php">Gestionar Eventos</a></li>
                    <li><a href="usuarios.php">Administrar Usuarios</a></li>
                </ul>
            <?php elseif ($rol === "promotor"): ?>
                <h3>Gestión de tus eventos</h3>
                <ul>
                    <li><a href="mis_eventos.php">Ver mis eventos</a></li>
                    <li><a href="crear_evento.php">Crear nuevo evento</a></li>
                </ul>
            <?php elseif ($rol === "premium"): ?>
                <h3>Beneficios Premium</h3>
                <p>¡Tienes acceso VIP a eventos y bebida ilimitada!</p>
            <?php endif; ?>

            <p><a href="logout.php">Cerrar sesión</a></p>
        </main>

        <?php require("includes/vistas/comun/sidebarDer.php"); ?>
        <?php require("includes/vistas/comun/pie.php"); ?>
    </div>
</body>
</html>
