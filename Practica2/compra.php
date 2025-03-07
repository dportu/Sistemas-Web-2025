<?php
    include("info_evento.php");
    
    function seleccionarEntrada($id_evento) {
        $evento = getEvento($id_evento);
        
        echo "<h2>[ " . htmlspecialchars($evento['nombre']) . " ]</h2>";
        echo "<img src='" . htmlspecialchars($evento['imagen']) . "' alt='Imagen del evento'>";
        echo "<p>" . $evento['fecha_inicio'] . (!empty($evento['fecha_fin']) ? " al " . $evento['fecha_fin'] : "") . "</p>";
        echo !empty($evento['ubicacion']) ? "<p><strong>Ubicacion:</strong> " . htmlspecialchars($evento['ubicacion']) . "</p>" : "";
        echo "<p>" . $evento['precio'] . " €</p>";
        // Añadir botones para incrementar o decrementar número de entradas
        echo "<p><strong>Numero de entradas: 0 </strong></p>";
    }
?>

<?php
    
    function comprarEntrada($id_evento) {
        echo "<p> Actualmente no disponible x o x<p>";
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
            sleccionarEntradas($id_evento);
        ?>

        <a href="evento.php?id=<?php echo $id_evento; ?>">
            <button type="button">Cancelar</button>
        </a>

        <form method="POST">
            <button type="submit" name="comprar">Continuar</button>
        </form>

        <?php
            if (isset($_POST['comprar'])) {
                comprarEntrada($id_evento);
            }
        ?>

    </main>
    
    <?php require("includes/vistas/comun/sidebarDer.php"); ?>
    <?php require("includes/vistas/comun/pie.php"); ?>
    
    </div>
</body>
</html>