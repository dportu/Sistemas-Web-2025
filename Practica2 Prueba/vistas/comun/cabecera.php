
<header>
    <h1>Eventia Inicio de Sesion</h1>
    <div class="saludo">
        <?php
        if (isset($_SESSION['login'])) {
            echo 'Bienvenido, '.$_SESSION['nombre'].'! <a href="logout.php">Logout</a>';
        } else {
            echo 'Usuario desconocido. <a href="login.php">Iniciar sesión</a>';
        }
        ?>
    </div>
</header>