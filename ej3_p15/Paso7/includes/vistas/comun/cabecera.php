
<?php

function mostrarSaludo() {
	$rutaApp = RUTA_APP;
	$html='';
	if (isset($_SESSION["login"]) && ($_SESSION["login"]===true)) {
		return "Bienvenido, {$_SESSION['username']} <a href='{$rutaApp}/logout.php'>(salir)</a>";
	} else {
		return "Usuario desconocido. <a href='{$rutaApp}/login.php'>Login</a> <a href='{$rutaApp}/registro.php'>Registro</a>";
	}
	return $html;
}
?>
<header>
	<h1>Mi gran página web</h1>
	<div class="saludo">
	<?= mostrarSaludo() ?>
	</div>
</header>