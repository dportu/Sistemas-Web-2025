<?php
require_once __DIR__.'/includes/config.php';

$tituloPagina = 'Portada';

$contenidoPrincipal = <<<EOS
<h1>Página principal</h1>
<p> Aquí está el contenido público, visible para todos los usuarios. </p>
EOS;

if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
    // Usuario registrado
    if (isset($_SESSION["username"])) {
        $contenidoPrincipal = <<<EOS
        <h1>Bienvenido, {$_SESSION["username"]}</h1>
        EOS;
    } else {
        $contenidoPrincipal = <<<EOS
        <h1>Bienvenido</h1>
        <p>No se pudo cargar tu nombre de usuario.</p>
        EOS;
    }
}

require __DIR__.'/includes/vistas/plantillas/plantilla.php';