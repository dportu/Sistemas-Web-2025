<?php
require_once __DIR__.'/includes/config.php';
use es\ucm\fdi\aw\eventos\Evento;
use es\ucm\fdi\aw\eventos\FormularioEditarEvento;
use es\ucm\fdi\aw\usuarios\Usuario;
use es\ucm\fdi\aw\Aplicacion;

$app = Aplicacion::getInstance();

// Verificar permisos
if (!$app->usuarioLogueado() || !$app->tieneRol(Usuario::ADMIN_ROLE)) {
    header("Location: index.php");
    exit();
}

// Obtener ID del evento
$idEvento = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Configurar HTML
$tituloPagina = 'Editar Evento';
$contenidoPrincipal = '';

try {
    $formulario = new FormularioEditarEvento($idEvento);
    
    // El método gestiona procesa el formulario o genera el HTML según corresponda
    $htmlFormulario = $formulario->gestiona();
    
    $contenidoPrincipal .= $htmlFormulario;
    
} catch (\Exception $e) {
    $contenidoPrincipal .= '<div class="error">' . $e->getMessage() . '</div>';
}

require __DIR__.'/includes/vistas/plantillas/plantilla.php';