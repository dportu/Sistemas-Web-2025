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

   // En la sección de contenidoPrincipal, reemplaza la tabla de eventos con:
$contenidoPrincipal = <<<EOS
<div class="dashboard-grid">
    <h1 class="dashboard-titulo">Panel de Administración</h1>
    
    <div class="grid-container">
        <!-- Módulo Eventos -->
        <div class="grid-item eventos">
            <div class="icono">📅</div>
            <h3>Gestionar Eventos</h3>
            <p>Organiza conciertos y actividades</p>
            <a href="moderar_eventos.php" class="boton-grid">Acceder</a>
        </div>

        <!-- Módulo Foro -->
        <div class="grid-item foro">
            <div class="icono">💬</div>
            <h3>Moderar Foro</h3>
            <p>Supervisa discusiones de usuarios</p>
            <a href="moderar_mensajes.php" class="boton-grid">Acceder</a>
        </div>

        <!-- Módulo Valoraciones -->
        <div class="grid-item valoraciones">
            <div class="icono">⭐</div>
            <h3>Moderar Valoraciones</h3>
            <p>Administra opiniones de usuarios</p>
            <a href="moderar_valoraciones.php" class="boton-grid">Acceder</a>
        </div>
    </div>

    <style>
        /* Estilos basados en CSS Grid (Tema 4.2) */
        .dashboard-grid {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 20px;
        }

        .dashboard-titulo {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 2rem;
            font-size: 2.2em;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            padding: 1rem;
        }

        .grid-item {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s;
            border: 1px solid #e0e0e0;
            text-align: center;
        }

        .grid-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }

        .icono {
            font-size: 2.5em;
            margin-bottom: 1rem;
        }

        .grid-item h3 {
            color: #2c3e50;
            margin: 0.5rem 0;
            font-size: 1.4em;
        }

        .grid-item p {
            color: #7f8c8d;
            margin-bottom: 1.5rem;
        }

        .boton-grid {
            display: inline-block;
            padding: 10px 25px;
            background: #3498db;
            color: white !important;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s;
        }

        .boton-grid:hover {
            background: #2980b9;
        }

      
    </style>
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