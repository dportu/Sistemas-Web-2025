<?php 
require_once __DIR__.'/includes/config.php';

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\foro\MensajeForo;
use es\ucm\fdi\aw\foro\FormularioForo;
use es\ucm\fdi\aw\eventos\Evento;

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
    
    $referenciaPadre = '';
    if ($mensaje->getParentId()) {
        $mensajePadre = MensajeForo::getMensajePorId($mensaje->getParentId());
        if ($mensajePadre) {
            $referenciaPadre = <<<EOS
                <div class="referencia-padre">
                    Respondiendo a @{$mensajePadre->getAutor()}
                    </a> en "<span class="titulo-padre">{$mensajePadre->getTitulo()}</span>"
                </div>
            EOS;
        }
    }

    $mensajeId = $mensaje->getId();
    $event = Evento::getNombrePorId($mensaje->getEvento());
    if($event === null) {
        $event = 'General';
    }
    $html = <<<EOS
    <div class="fondo" id="mensaje-{$mensajeId}">
        {$referenciaPadre}
        <div class="mensaje">
            <h4 class="titulo-mensaje">
                Foro: <a href = {$aplicacion->buildUrl('foro.php', ['id' => $mensaje->getEvento()])} {$mensaje->getTitulo()}>
                $event
                </a>
            </h4>
            <div class="meta-mensaje">
                <span class="autor">@{$mensaje->getAutor()}</span>
                <span class="fecha">{$mensaje->getFechaPublicacion()}</span>
            </div>
        </div>
        
        <div class="mensaje">
            <p class="texto-mensaje"> Mensaje: "{$mensaje->getMensaje()}"</p>
    EOS;


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


$mensajesPrincipales = MensajeForo::getMensajes($id_evento);
if (empty($mensajesPrincipales)) {
    $contenidoPrincipal .= "<p class='sin-mensajes'>Todavía no hay mensajes.</p>";
} else {
    foreach ($mensajesPrincipales as $mensaje) {
        //Solo mostramos los mensajes pirncipales que no son respuestas
        if ($mensaje->getParentId() === null) {
            $contenidoPrincipal .= renderizarMensaje($mensaje, $aplicacion, $id_evento);
        }
       
    }
}


$form = new FormularioForo($id_evento);
$contenidoPrincipal .= <<<EOS
    <div class="nuevo-hilo">
        <h2>Iniciar nuevo tema</h2>
        {$form->gestiona()}
    </div>


EOS;

require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>