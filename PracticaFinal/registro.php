<?php
    require_once __DIR__.'/includes/config.php';
    use es\ucm\fdi\aw\usuarios\FormularioRegistro;

    $form = new FormularioRegistro();
    $htmlFormRegistro = $form->gestiona();

    $tituloPagina = 'Registro';
    $contenidoPrincipal = <<<EOS
        <div class="enlace-registro">
            <h4>Registro de usuario</h4>
            $htmlFormRegistro
        </div>
        <!-- Includes de los js -->
        <script src="js/jquery-3.7.1.min.js"></script>
        <script src="js/validacion.js"></script>
    EOS;

    require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>