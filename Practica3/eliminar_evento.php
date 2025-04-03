<?php
require_once __DIR__.'/includes/config.php';
use es\ucm\fdi\aw\eventos\Evento;
use es\ucm\fdi\aw\usuarios\Usuario;
use es\ucm\fdi\aw\Aplicacion;

$app = Aplicacion::getInstance();

// Verificar autenticación
if (!$app->usuarioLogueado()) {
    header("Location: login.php");
    exit();
}

// Obtener ID del evento
$idEvento = isset($_POST['id']) ? (int)$_POST['id'] : 0;

// Obtener el evento
$evento = Evento::buscaPorId($idEvento);
if (!$evento) {
    $app->putAtributoPeticion('error', 'Evento no encontrado');
    header("Location: index.php");
    exit();
}

// Verificar permisos
$usuarioActual = $app->nombreUsuario();
$esAdmin = $app->tieneRol(Usuario::ADMIN_ROLE);
$esPromotor = $app->tieneRol(Usuario::PROMOTOR_ROLE);
$esOrganizador = ($evento->getOrganizador() === $usuarioActual);

if ($esAdmin || ($esPromotor && $esOrganizador)) {
    try {
        $evento->eliminarEvento();
        
        $app->putAtributoPeticion('exito', 'Evento eliminado correctamente');
    } catch (Exception $e) {
        $app->putAtributoPeticion('error', 'Error al eliminar el evento: ' . $e->getMessage());
    }
} else {
    $app->putAtributoPeticion('error', 'No tienes permisos para esta acción');
}

header("Location: index.php");
exit();