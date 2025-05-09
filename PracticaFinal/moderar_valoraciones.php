<?php
require_once __DIR__.'/includes/config.php';
use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\valoraciones\Valoracion;
use es\ucm\fdi\aw\usuarios\Usuario;

$app = Aplicacion::getInstance();
$tituloPagina = 'Moderación de Valoraciones';
$rutaApp = RUTA_APP;
$usuarioActual = $app->nombreUsuario();

if ($app->tieneRol(Usuario::ADMIN_ROLE)) {
    $valoraciones = Valoracion::valoracionesEvento(null); // Obtener todas
    
    $tablaValoraciones = '';
    foreach ($valoraciones as $valoracion) {
        $botonEditar = ($valoracion->getUsername() === $usuarioActual)
            ? "<a href='editar_valoracion.php?id={$valoracion->getId()}' class='boton-editar'>Editar</a>"
            : "<span class='no-editable'>No editable</span>";

        $tablaValoraciones .= <<<EOS
        <tr>
            <td>{$valoracion->getUsername()}</td>
            <td>{$valoracion->getIdEvento()}</td>
            <td>{$valoracion->getNota()}/5</td>
            <td>{$valoracion->getComentario()}</td>
            <td>{$valoracion->getFecha()}</td>
            <td class="acciones-celda">
                $botonEditar
                <form action="{$rutaApp}/moderar_valoraciones.php" method="POST">
                    <input type="hidden" name="valoracion_id" value="{$valoracion->getId()}">
                    <button type="submit" name="accion" value="eliminar" 
                        class="boton-eliminar" 
                        onclick="return confirm('¿Eliminar esta valoración permanentemente?')">
                        Eliminar
                    </button>
                </form>
            </td>
        </tr>
        EOS;
    }

    $contenidoPrincipal = <<<EOS
    <style>
        /* Añade los mismos estilos que en moderar_mensajes.php */
        .tabla-moderacion {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .tabla-moderacion th {
            background: #2c3e50;
            color: white;
            padding: 12px;
        }
        .tabla-moderacion td {
            padding: 10px;
            border-bottom: 1px solid #ecf0f1;
            max-width: 300px;
            word-wrap: break-word;
        }
        .boton-editar {
            background: #3498db;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 10px;
        }
        .boton-eliminar {
            background: #e74c3c;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
    <div class="admin-section">
        <h2>Moderación de Valoraciones</h2>
        <table class="tabla-moderacion">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>ID Evento</th>
                    <th>Nota</th>
                    <th>Comentario</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                $tablaValoraciones
            </tbody>
        </table>
    </div>
    EOS;

    // Procesar eliminación
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
        $valoracionId = $_POST['valoracion_id'] ?? null;
        if ($_POST['accion'] === 'eliminar' && $valoracionId) {
            Valoracion::eliminarValoracion($valoracionId);
            header("Location: {$rutaApp}/moderar_valoraciones.php");
            exit();
        }
    }
} else {
    $contenidoPrincipal = <<<EOS
    <div class="acceso-denegado">
        <h2>Acceso Denegado!</h2>
        <p>No tienes permisos de administrador.</p>
        <a href="index.php" class="boton-volver">Volver al Inicio</a>
    </div>
    EOS;
}

require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>