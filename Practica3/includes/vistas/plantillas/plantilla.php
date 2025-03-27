<!DOCTYPE html>
<html lang="es">
    <head>
	    <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="css/estilo.css">
        <title><?= $tituloPagina ?></title>
    </head>

    <body>
        <div id="contenedor"> <!-- Inicio del contenedor -->
            <?php
                define('PATH', dirname(__DIR__).'/comun/');
                require(PATH."cabecera.php"); // Cabecera de la página web
                require(PATH."sidebarIzq.php"); // Menú de navegación
            ?>

            <!-- Parte central de la página web -->
            <main>
                <article>
                    <?= $contenidoPrincipal ?>
                </article>
            </main>

            <?php
                require(PATH."sidebarDer.php"); // Navegación en la parte derecha
                require(PATH."pie.php"); // Pie de página
            ?>
        </div> <!-- Fin del contenedor -->
    </body>
</html>