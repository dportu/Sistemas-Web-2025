<?php
    require_once __DIR__.'/includes/config.php';
    use es\ucm\fdi\aw\eventos\FormularioEditarEvento;
    use es\ucm\fdi\aw\Aplicacion;

    $app = Aplicacion::getInstance();
    $idEvento = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $tituloPagina = 'Editar Evento';
    $contenidoPrincipal = '';

    try {
        $formulario = new FormularioEditarEvento($idEvento);
        $htmlFormulario = $formulario->gestiona();
        $contenidoPrincipal .= $htmlFormulario;
        
    } catch (\Exception $e) {
        $contenidoPrincipal .= '<div class="error">' . $e->getMessage() . '</div>';
    }

    require __DIR__.'/includes/vistas/plantillas/plantilla.php';