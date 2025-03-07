<?php
    include("info_evento.php");
    
    function mostrarValoraciones($id_evento) {
        $evento = getEvento($id_evento);
        //$valoraciones = getValoraciones($id_evento);

        echo "<h2>Valoraciones [ " . htmlspecialchars($evento['nombre']) . " ]</h2>";
        echo "<p>Ninguno de nuestros usuarios ha valorado todavía este evento . . .</p>";
        echo "<p> ( anímate!! podrías ser tú el primero c: ) </p>";
    }

    // Hasta que no se añada soporte para las valoraciones en la base de datos esto no funciona :/

    /*function mostrarValoraciones($id_evento) {
        $evento = getEvento($id_evento);
        $valoraciones = getValoraciones($id_evento);

        echo "<h2>Valoraciones [ " . htmlspecialchars($evento['nombre']) . " ]</h2>";

        if (!empty($valoraciones)) {
            echo "<ul>";
            foreach ($valoraciones as $valoracion) {
                echo "<li>";
                echo "<p>" . htmlspecialchars($valoracion['usuario']) . "<p>";
                echo "<p><strong>Valoración:</strong> " . htmlspecialchars($valoracion['valoracion']) . " / 5<p>";
                echo "<p><strong>Comentario:</strong> " . htmlspecialchars($valoracion['comentario']) . "</p>";
                echo "</li>";
            }
            echo "</ul>";
        }
        else {
            echo "<p>Ninguno de nuestros usuarios ha valorado todavía este evento . . .</p>";
            echo "<p> ( anímate!! podrías ser tú el primero c: ) </p>";
        }
    } */
?>

<?php
    function añadirValoracion($id_evento) {
        echo "<p> Actualmente no disponible ://<p>";
    }
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
            $id_evento = $_GET['id'];
            mostrarValoraciones($id_evento);
        ?>
        
        <form method="POST">
            <button type="submit" name="valorar">Añadir valoración</button>
        </form>

        <?php
            // Comprobar si el formulario ha sido enviado
            if (isset($_POST['valorar'])) {
                añadirValoracion($id_evento);
            }
        ?>
    </main>
    
    <?php require("includes/vistas/comun/sidebarDer.php"); ?>
    <?php require("includes/vistas/comun/pie.php"); ?>
    
    </div>
</body>
</html>