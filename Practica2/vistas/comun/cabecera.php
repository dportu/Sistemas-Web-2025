<?php 
function mostrarSaludo() {
	if (isset($_SESSION['login'])) {
        echo 'Bienvenido, '.$_SESSION["usuario_nombre"].'! <a href="logout.php">Logout</a>';
    } else {
        echo 'Usuario desconocido. <a href="login.php">Iniciar sesión</a>';
    }
}
?>

<header>
    <h1>Eventia</h1>
    <div class="saludo">
        <?php
        mostrarSaludo();
        ?>
    </div>
</header>