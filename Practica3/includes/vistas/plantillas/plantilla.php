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
                require(PATH."header.php"); // Cabecera de la página web
            ?>

            <!-- Parte central de la página web -->
            <main>
                
                <article>
                    <?= $contenidoPrincipal ?>
                </article>
                
            </main>

            <?php
                require(PATH."sidebar.php"); // Menú de navegación
                require(PATH."footer.php"); // Pie de página
            ?>
        </div> <!-- Fin del contenedor -->
    </body>
</html>