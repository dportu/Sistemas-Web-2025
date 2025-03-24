<?php
require_once __DIR__.'/includes/config.php';

unset($_SESSION['username']);
unset($_SESSION['login']);
unset($_SESSION['usuario_email']);

//Para asegurannos de que se borra la sesión
session_destroy();

$tituloPagina = 'Logout';

$contenidoPrincipal = <<<EOS
<h1>Hasta pronto!</h1>
EOS;

require __DIR__.'/includes/vistas/plantillas/plantilla.php';
