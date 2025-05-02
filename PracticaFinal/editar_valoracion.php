<?php
    require_once __DIR__.'/includes/config.php';

    use es\ucm\fdi\aw\valoraciones\FormularioEditarValoracion;

    $tituloPagina = 'Editar Valoracion';

    $idMensaje = $_GET['id'] ?? null;

    $form = new FormularioEditarValoracion($idMensaje);
    $htmlForm = $form->gestiona();

    $contenidoPrincipal = <<<EOS
        <h2>Editar mensaje</h2>
        $htmlForm
    EOS;

    require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>
