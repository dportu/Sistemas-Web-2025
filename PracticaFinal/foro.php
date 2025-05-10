<?php 
require_once __DIR__.'/includes/config.php';

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\foro\MensajeForo;
use es\ucm\fdi\aw\foro\FormularioForo;

$tituloPagina = 'Foro';
$contenidoPrincipal = '';
$aplicacion = Aplicacion::getInstance();
$id_evento = $_GET['id'] ?? null;

// Manejar eliminación de mensajes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    $mensaje_id = $_POST['mensaje_id'] ?? null;

    if ($accion === 'eliminar' && $mensaje_id) {
        $mensaje = MensajeForo::getMensajePorId($mensaje_id);
        $app = Aplicacion::getInstance();
        
        if ($mensaje && ($app->nombreUsuario() === $mensaje->getAutor() || $app->esAdmin())) {
            MensajeForo::eliminarMensaje($mensaje_id);
            $id_evento = $mensaje->getEvento();
            header("Location: foro.php" . ($id_evento ? "?id=$id_evento" : ""));
            exit();
        }
    }
}

function renderizarMensaje($mensaje, $aplicacion, $id_evento, $nivel = 0) {
    // Referencia al mensaje padre
    $referenciaPadre = '';
    if ($mensaje->getParentId()) {
        $mensajePadre = MensajeForo::getMensajePorId($mensaje->getParentId());
        if ($mensajePadre) {
            $referenciaPadre = <<<EOS
                <div class="referencia-padre">
                    Respondiendo a <a href="#mensaje-{$mensajePadre->getId()}" class="enlace-padre">
                        @{$mensajePadre->getAutor()}
                    </a> en "<span class="titulo-padre">{$mensajePadre->getTitulo()}</span>"
                </div>
            EOS;
        }
    }

    $html = <<<EOS
    <div class="mensaje" id="mensaje-{$mensaje->getId()}">
        {$referenciaPadre}
        <div class="cabecera-mensaje">
            <h3 class="titulo-mensaje">
                <a href="#mensaje-{$mensaje->getId()}" class="enlace-titulo">{$mensaje->getTitulo()}</a>
            </h3>
            <div class="meta-mensaje">
                <span class="autor">@{$mensaje->getAutor()}</span>
                <span class="fecha">{$mensaje->getFechaPublicacion()}</span>
            </div>
        </div>
        
        <div class="contenido-mensaje">
            <p class="texto-mensaje">{$mensaje->getMensaje()}</p>
    EOS;

    // Botones de acción
    if ($aplicacion->usuarioLogueado() && ($aplicacion->nombreUsuario() === $mensaje->getAutor() || $aplicacion->esAdmin())) {
        $html .= <<<EOS
            <div class="acciones-mensaje">
                <a href="{$aplicacion->buildUrl('editar_mensajeForo.php', ['id' => $mensaje->getId()])}" class="boton editar">
                    Editar
                </a>
                <form method="POST" class="form-eliminar">
                    <input type="hidden" name="mensaje_id" value="{$mensaje->getId()}">
                    <button type="submit" name="accion" value="eliminar" class="boton eliminar">
                        Eliminar
                    </button>
                </form>
            </div>
        EOS;
    }

    // Formulario de respuesta
    if ($aplicacion->usuarioLogueado()) {
        $form = new FormularioForo($id_evento, $mensaje->getId());
        $html .= <<<EOS
            <details class="contenedor-respuesta">
                <summary class="boton-respuesta">Responder</summary>
                <div class="formulario-respuesta">
                    {$form->gestiona()}
                </div>
            </details>
        EOS;
    }

    // Respuestas
    $respuestas = MensajeForo::getRespuestas($mensaje->getId());
    if (!empty($respuestas)) {
        $html .= '<div class="respuestas">';
        foreach ($respuestas as $respuesta) {
            $html .= renderizarMensaje($respuesta, $aplicacion, $id_evento, $nivel + 1);
        }
        $html .= '</div>';
    }

    $html .= "</div></div>";
    return $html;
}

// Contenido principal
$mensajesPrincipales = MensajeForo::getMensajes($id_evento);
if (empty($mensajesPrincipales)) {
    $contenidoPrincipal .= "<p class='sin-mensajes'>Todavía no hay mensajes.</p>";
} else {
    foreach ($mensajesPrincipales as $mensaje) {
        // Solo mostrar mensajes que no son respuestas
        if ($mensaje->getParentId() === null) {
            // Renderizar el mensaje
            $contenidoPrincipal .= renderizarMensaje($mensaje, $aplicacion, $id_evento);
        }
        //$contenidoPrincipal .= renderizarMensaje($mensaje, $aplicacion, $id_evento);
    }
}

// Formulario principal
$form = new FormularioForo($id_evento);
$contenidoPrincipal .= <<<EOS
    <div class="nuevo-hilo">
        <h2>Iniciar nuevo tema</h2>
        {$form->gestiona()}
    </div>

    <style>
        .mensaje {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            padding: 20px;
            position: relative;
        }

        .referencia-padre {
            font-size: 0.9em;
            color: #7f8c8d;
            margin-bottom: 10px;
            padding-left: 15px;
            border-left: 3px solid #e0e0e0;
        }

        .cabecera-mensaje {
            margin-bottom: 15px;
        }

        .titulo-mensaje {
            margin: 0;
            font-size: 1.3em;
        }

        .meta-mensaje {
            display: flex;
            gap: 15px;
            color: #7f8c8d;
            font-size: 0.9em;
            margin-top: 8px;
        }

        .acciones-mensaje {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }

        .boton {
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }

        .editar { background: #3498db; color: white; }
        .eliminar { background: #e74c3c; color: white; }

        .contenedor-respuesta {
            margin-top: 15px;
        }

        .boton-respuesta {
            background: none;
            border: none;
            color: #2ecc71;
            cursor: pointer;
            padding: 8px 0;
        }

        .nuevo-hilo {
            margin-top: 30px;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 8px;
        }
    </style>
EOS;

require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>