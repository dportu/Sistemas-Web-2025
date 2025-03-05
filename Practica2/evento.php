<?php

    include("info_evento.php");

    function mostrarInfoEvento() {
        $id_evento = $_GET['id'];
        $evento = getEvento($id_evento);

        echo "<h2>" . htmlspecialchars($evento['nombre']) . "</h2>";
        echo "<p><strong>Fecha:</strong> " . $evento['fecha_inicio'] . (!empty($evento['fecha_fin']) ? " al " . $evento['fecha_fin'] : "") . "</p>";
        echo !empty($evento['lugar']) ? "<p><strong>Lugar:</strong> " . htmlspecialchars($evento['lugar']) . "</p>" : "";
        echo !empty($evento['organizador']) ? "<p><strong>Organizador:</strong> " . htmlspecialchars($evento['organizador']) . "</p>" : "";
        echo !empty($evento['descripcion']) ? "<p><strong>Descripción:</strong> " . htmlspecialchars($evento['descripcion']) . "</p>" : "";
    }
?>
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
    <link id="estilo" rel="stylesheet" type="text/css" href="CSS/estilo.css"/>
</head>
<body>

    <div id="contenedor">

    <?php require("includes/vistas/comun/cabecera.php"); ?>

    <?php require("includes/vistas/comun/sidebarIzq.php"); ?>

    <main>
        <?php 
            mostrarInfoEvento() 
        ?>
        <a href="compra.php">
            <button type="button">Compra</button>
        </a>

        <a href="valorar.php">
            <button type="button">Valorar</button>
        </a>

        <a href="foro_evento.php">
            <button type="button">Foro Evento</button>
        </a>

    </main>
    
    <?php require("includes/vistas/comun/sidebarDer.php"); ?>
    <?php require("includes/vistas/comun/pie.php"); ?>
    
    </div>
</body>
</html>