<?php
    require_once __DIR__.'/includes/config.php';

    use es\ucm\fdi\aw\foro\FormularioEditarMensaje;

    $tituloPagina = 'Editar Mensaje';

    $idMensaje = $_GET['id'] ?? null;

    $form = new FormularioEditarMensaje($idMensaje);
    $htmlForm = $form->gestiona();

    $contenidoPrincipal = <<<EOS
        <h1>Editar Mensaje</h1>
        $htmlForm
    EOS;

    require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>
