<?php
require_once __DIR__.'/includes/config.php';
use es\ucm\fdi\aw\eventos\FormularioAnyadirEvento;
use es\ucm\fdi\aw\usuarios\Usuario;
use es\ucm\fdi\aw\Aplicacion;

$app = Aplicacion::getInstance();

// Verificar permisos
if (!$app->usuarioLogueado() || !$app->tieneRol(Usuario::ADMIN_ROLE)) {
    header("Location: index.php");
    exit();
}

// Configurar contenido
$tituloPagina = 'Añadir Evento';
$formulario = new FormularioAnyadirEvento();
$htmlFormulario = $formulario->gestiona();
$contenidoPrincipal = $htmlFormulario;

require __DIR__.'/includes/vistas/plantillas/plantilla.php';