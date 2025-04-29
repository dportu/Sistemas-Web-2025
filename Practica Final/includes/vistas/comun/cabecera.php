<?php

function mostrarSaludo() {
	$rutaApp = RUTA_APP;
	$html='';
	if (isset($_SESSION["login"]) && ($_SESSION["login"]===true)) {
		return "Bienvenido, {$_SESSION['username']} <a href='{$rutaApp}/logout.php'>(salir)</a>";
	} else {
		return "Usuario desconocido. <a href='login.php'>Login</a>";
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