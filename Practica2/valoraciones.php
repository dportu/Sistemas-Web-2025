<?php
    include("info_evento.php");
    
    // Valoraciones añadidas en la BD :)) pero no lo he podido probar desde este ordenador
    function mostrarValoraciones($id_evento) {
        $evento = getEvento($id_evento);
        $valoraciones = getValoraciones($id_evento);

        echo "<h2>Valoraciones [ " . htmlspecialchars($evento['nombre']) . " ]</h2>";

        if (!empty($valoraciones)) {
            echo "<ul>";
            foreach ($valoraciones as $valoracion) {
                echo "<li>";
                echo "<p>" . htmlspecialchars($valoracion['username']) . "<p>";
                echo "<p><strong>Valoración:</strong> " . htmlspecialchars($valoracion['nota']) . " / 5<p>";
                echo "<p><strong>Comentario:</strong> " . htmlspecialchars($valoracion['comentario']) . "</p>";
                echo "<p>" . htmlspecialchars($valoracion['fecha']) . "<p>";
                echo "</li>";
            }
            echo "</ul>";
        }
        else {
            echo "<p>Ninguno de nuestros usuarios ha valorado todavía este evento . . .</p>";
            echo "<p> ( anímate!! podrías ser tú el primero c: ) </p>";
        }
    }
?>

<?php
    // INSERT INTO valoraciones (id_evento, id_usuario, nota, comentario) VALUES (?, ?, ?, ?);
    function añadirValoracion($id_evento) {
        echo "<p> Actualmente no disponible ://<p>";
    }
?>

<?php
    // UPDATE valoraciones SET nota = ?, comentario = ? WHERE id = ? AND id_usuario = ?;
    function editarValoracion($id_evento) {
        echo "<p> Actualmente no disponible ://<p>";
    }
?>

<?php
    // DELETE FROM valoraciones WHERE id = ? AND id_usuario = ?;
    function eliminarValoracion($id_evento) {
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