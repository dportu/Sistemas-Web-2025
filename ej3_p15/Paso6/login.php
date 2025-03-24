<?php
require_once __DIR__.'/includes/config.php';
require_once __DIR__.'/includes/FormularioLogin.php';


$formLogin = new FormularioLogin();
$htmlFormLogin = $formLogin->gestiona();

$tituloPagina = 'Login';
$contenidoPrincipal = <<<EOS
<div class="login-page">
    <main>
        $htmlFormLogin
        <div class="enlace-registro">
            <a href="registro.php">¿No tienes cuenta? Regístrate aquí</a>
        </div>
    </main>
</div>
EOS;

require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>