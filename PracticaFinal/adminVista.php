<?php
use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\eventos\Evento;
use es\ucm\fdi\aw\usuarios\Usuario;
use es\ucm\fdi\aw\admin\Admin;

require_once __DIR__.'/includes/config.php';

$tituloPagina = 'Panel de Administración';
$app = Aplicacion::getInstance();
$rutaApp = RUTA_APP;

if ($app->tieneRol(Usuario::ADMIN_ROLE)) {
    

   // En la sección de contenidoPrincipal, reemplaza la tabla de eventos con:
$contenidoPrincipal = <<<EOS
<div class="admin-panel">
    <h2 class="titulo-seccion">Panel de Administración</h2>
    
    <div class="grid-modulos">

        <div class="modulo-card evento-card">
            <div class="icono-modulo">📅</div>
            <h3>Gestionar Eventos</h3>
            <p>Organiza conciertos y actividades</p>
            <a href="moderar_eventos.php" class="boton-accion editar">Acceder</a>
        </div>


        <div class="modulo-card foro-card">
            <div class="icono-modulo">💬</div>
            <h3>Moderar Foro</h3>
            <p>Supervisa discusiones de usuarios</p>
            <a href="moderar_mensajes.php" class="boton-accion editar">Acceder</a>
        </div>


        <div class="modulo-card valoraciones-card">
            <div class="icono-modulo">⭐</div>
            <h3>Moderar Valoraciones</h3>
            <p>Administra opiniones de usuarios</p>
            <a href="moderar_valoraciones.php" class="boton-accion editar">Acceder</a>
        </div>
    </div>
</div>
EOS;

} else {
    $contenidoPrincipal = <<<EOS
    <div class="acceso-denegado">
        <h2>Acceso Denegado!</h2>
        <p>No tienes permisos suficientes para acceder a esta sección.</p>
        <a href="index.php" class="boton-volver">Volver al Inicio</a>
    </div>
    EOS;
}

require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>