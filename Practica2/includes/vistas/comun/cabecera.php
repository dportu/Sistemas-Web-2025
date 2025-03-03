<?php 
function mostrarSaludo() {
	if (isset($_SESSION['login'])) {
        echo 'Bienvenid@ '.$_SESSION["usuario_nombre"].'! <a href="logout.php">Cerrar sesión</a>';
    } else {
        echo 'Usuario desconocido. <a href="login.php">Iniciar sesión</a>';
    }
}
?>

<header>
    <h1>Eventia</h1>
    <div class="saludo">
        <?php
            $pagina_actual = basename($_SERVER['PHP_SELF']);
            if ($pagina_actual != "login.php") mostrarSaludo();
            else echo '<a href="index.php">Inicio</a>'
        ?>
    </div>
</header>