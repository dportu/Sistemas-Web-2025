<?php
require_once __DIR__.'/includes/config.php';
use es\ucm\fdi\aw\usuarios\FormularioRegistro;

$form = new FormularioRegistro();
$htmlFormRegistro = $form->gestiona();

$tituloPagina = 'Registro';
$contenidoPrincipal = <<<EOS
    <h4>Registro de usuario</h4>
    $htmlFormRegistro
EOS;

require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>