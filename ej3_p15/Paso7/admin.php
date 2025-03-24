<?php
require_once __DIR__.'/includes/config.php';

$tituloPagina = 'Administrar';
$contenidoPrincipal = '';

if (isset($_SESSION["usuario_rol"]) && $_SESSION["usuario_rol"] === "administrador") {
    // Si el usuario es administrador, mostramos su consola
    $contenidoPrincipal .= <<<EOS
    <h1>Consola de administración</h1>
    <p>Aquí estarían todos los controles de administración</p>
    <h2>Controles de Administración</h2>
    <ul>
        <li>Gestión de usuarios</li>
        <li>Gestión de contenidos</li>
        <li>Estadísticas del sitio</li>
        <li>Configuración del sistema</li>
    </ul>
EOS;
} else {
    // Usuario no es administrador: Acceso denegado
    $contenidoPrincipal .= <<<EOS
    <h1>Acceso denegado!</h1>
    <p>No tienes permisos suficientes para administrar la web.</p>
EOS; // <-- Sin indentación antes de EOS
}

require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>