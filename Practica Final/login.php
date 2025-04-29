<?php
  require_once __DIR__.'/includes/config.php';
  // require_once __DIR__.'/includes/clases/Usuarios/FormularioLogin.php';
  use es\ucm\fdi\aw\usuarios\FormularioLogin;

  $form = new FormularioLogin();
  $htmlFormLogin = $form->gestiona();

  $tituloPagina = 'Login';

  $contenidoPrincipal = <<<EOS
  <div class="enlace-registro">
    <h3>Iniciar sesión </h3>
    $htmlFormLogin
    <a href="registro.php">¿No tienes cuenta? Regístrate aquí</a>
  </div>
  EOS;

  require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>