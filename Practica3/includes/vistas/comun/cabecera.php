<?php
use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\usuarios\Usuario;

function mostrarSaludo() {
	$rutaApp = RUTA_APP;
	$html='';
	
	if (isset($_SESSION["login"]) && ($_SESSION["login"]===true)) {
		
		$app = Aplicacion::getInstance();
		$nombreUsuario = $app->nombreUsuario();
		//conseguimos el usuario
		$usuario = Usuario::buscaUsuario($nombreUsuario);

		$puntos = $usuario->getPuntos();
		$html = "Bienvenido, {$_SESSION['username']} <a href='{$rutaApp}/logout.php'>(salir)</a> Puntos:{$puntos}";
	} 
	else {
		$html = "Usuario desconocido. <a href='login.php'>Login</a>";
	}

	return $html;
}
?>
<header>
	<h1>Eventia</h1>
	<div class="saludo">
	<?= mostrarSaludo() ?>
	</div>
</header>