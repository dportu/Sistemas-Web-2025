<?php
require_once __DIR__.'/includes/config.php';
require_once __DIR__.'/includes/FormularioRegistro.php';

$formRegistro = new FormularioRegistro();
$htmlFormRegistro = $formRegistro->gestiona();

$tituloPagina = 'Registro';
$contenidoPrincipal = <<<EOS
<div class="registro-page"><main>$htmlFormRegistro</main>
       
   
</div>
EOS;

require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>