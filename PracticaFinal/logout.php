<?php 
	
	require_once __DIR__.'/includes/config.php';

	// Borrar las variables
    // TODO: borrar las variables adaptada a nuestra tabla
	unset($_SESSION["nombre"]);
	unset($_SESSION["login"]);
	if (isset($_SESSION["esAdmin"])){
		unset($_SESSION["esAdmin"]);
	}
	// Destruir la sesion
	session_destroy();

	// Vista

	$tituloPagina = 'Logout';

	$contenidoPrincipal = <<<EOS
	<div class="enlace-registro">
		<h1>Sesión cerrada</h1>
		<p> Gracias por visitar nuestra web. Hasta pronto. </p>
	</div>
	EOS;

	require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>