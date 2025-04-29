<?php
    require_once __DIR__.'/includes/config.php';

    use es\ucm\fdi\aw\Aplicacion;
    use es\ucm\fdi\aw\usuarios\Usuario;

    $app = Aplicacion::getInstance();

    if (!$app->usuarioLogueado()) {
        $app->redirige($app->buildUrl('login.php'));
    }

    $usuario = Usuario::buscaUsuario($app->nombreUsuario());
    $nombre = htmlspecialchars($usuario->getUsername());
    $email = htmlspecialchars($usuario->getEmail());
    $rol = htmlspecialchars($usuario->getRol());
    $puntos = htmlspecialchars($usuario->getPuntos());

    // Usamos la funcion build url , que nos venia en app , 
    $enlaceEditar = $app->buildUrl('editar_perfil.php');
    $enlaceLogout = $app->buildUrl('logout.php');
    $enlaceAdmin = $app->buildUrl('adminVista.php');
    $enlaceCompras = $app->buildUrl('mis_compras.php');


    $opcionesEspecificas = '';
    if ($rol === 'administrador' || $rol === 'promotor') {
        $opcionesEspecificas = <<<EOS
            <h2>Opciones de Administrador</h2>
            <ul>
                <li>Rol: $rol</li>
                <li><a href="$enlaceAdmin" class="boton_admin">Consola de administración</a></li>
            </ul>
        EOS;
    } else {
        $opcionesEspecificas = <<<EOS
            <h2>Opciones de Usuario</h2>
            <a href="$enlaceCompras">Mis Compras</a>
            <p>Puntos acumulados: $puntos </p>
        EOS;
    }

    $tituloPagina = "Perfil de $nombre";
    $contenidoPrincipal = <<<EOS
         <h2>Perfil de usuario</h2>
        <article class="perfil-usuario">
            <h3>Bienvenido, $nombre!</h3>
            <div class="info-perfil">
                <p><strong>Email:</strong> $email</p>
                <a href="$enlaceEditar" class="boton-editar"> Editar perfil</a>
            </div>
            
            <section class="opciones-perfil">
                $opcionesEspecificas
            </section>
            
            <div class="acciones-secundarias">
                <a href="$enlaceLogout" class="boton-logout"> Cerrar sesión</a>
            </div>
        </article>
    EOS;

    require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>