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
    
    $eventos = Evento::getEventos();
    $tablaEventos = '';
    foreach ($eventos as $evento) {
        $tablaEventos .= <<<EOS
        <tr>
            <td>{$evento->getNombre()}</td>
            <td>{$evento->getPrecio()}</td>
            <td>{$evento->getFecha()}</td>
            <td>{$evento->getUbicacion()}</td>
            <td>
                <a href="editar_evento.php?id={$evento->getId()}" class="boton-editar">Editar</a>
                <a href="eliminar_evento.php?id={$evento->getId()}" class="boton-eliminar">Eliminar</a>
            </td>
        </tr>
        EOS;
    }

    $contenidoPrincipal = <<<EOS
    <div class="enlace-registro">
        <h2>Consola de Administración</h2>
        
        <div class="admin-section">
            <h2>Gestión de Eventos</h2>
            <a href="{$rutaApp}/anyadir_evento.php" class="boton-crear">Crear Nuevo Evento</a>
            <table class="tabla-eventos">
                <thead>    
                    <tr>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Fecha Inicio</th>
                        <th>Ubicación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    $tablaEventos
                </tbody>
            </table>
        </div>

        <div class="admin-section">
            <h2>Moderación de Contenido</h2>
            <a href="moderar_mensajes.php" class="boton-moderar">Moderar Mensajes del Foro</a>
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