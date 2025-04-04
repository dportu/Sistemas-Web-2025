<?php
use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\usuarios\Usuario;

function mostrarSaludo() {
	$html='';
	
	if (isset($_SESSION["login"]) && ($_SESSION["login"]===true)) {
		
		$app = Aplicacion::getInstance();
		$nombreUsuario = $app->nombreUsuario();
		//conseguimos el usuario
		$usuario = Usuario::buscaUsuario($nombreUsuario);

		$html = "Bienvenido, {$_SESSION['username']}";
	} 
	else {
		$html = "Usuario desconocido. <a href='login.php'>Login</a>";
	}

	return $html;
}

?>
<header>
	<h1 onclick="window.location.href='index.php';">EVENTIA</h1>
	<div class="saludo">
	<?= mostrarSaludo() ?>
	</div>
</header>