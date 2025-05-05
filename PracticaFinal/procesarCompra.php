<?php
require_once __DIR__.'/includes/config.php';

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\eventos\Evento;
use es\ucm\fdi\aw\usuarios\Usuario;

$app = Aplicacion::getInstance();

// Verificar usuario y evento
if (!$app->usuarioLogueado() || !isset($_GET['id'])) {
    header('Location: login.php');
    exit();
}

$idEvento = (int)$_GET['id'];
$evento = Evento::buscaPorId($idEvento);
$usuario = Usuario::buscaUsuario($app->nombreUsuario());

if (!$evento || !$usuario) {
    header('Location: eventos.php');
    exit();
}

// Datos para el formulario
$entradasDisponibles = $evento->getEntradasDisponibles();
$puntosUsuario = $usuario->getPuntos();
$precioEvento = $evento->getPrecio();

// Plantilla del formulario
$tituloPagina = 'Procesar Compra - ' . $evento->getNombre();
$contenidoPrincipal = <<<EOS
<div class="formulario-compra">
    <h2>Comprar entradas para: <strong>{$evento->getNombre()}</strong></h2>
    
    <form action="compraEvento.php" method="POST">
        <input type="hidden" name="id_evento" value="$idEvento">
        
        <div class="campo">
            <label>Entradas disponibles: $entradasDisponibles</label>
            <input type="number" 
                   name="cantidad" 
                   id="input-cantidad"
                   min="1" 
                   max="$entradasDisponibles" 
                   value="1" 
                   required
                   data-precio="$precioEvento"
                   data-puntos="$puntosUsuario">
        </div>
        
        <div class="campo">
            <label>Usar puntos (tienes $puntosUsuario):</label>
            <input type="number" 
                   name="puntos" 
                   id="input-puntos" 
                   min="0" 
                   value="0">
            <small>1 punto = 1€ de descuento</small>
        </div>
        
        <div class="resumen">
            <p>Precio por entrada: $precioEvento €</p>
            <p id="precio-total">Total estimado: $precioEvento €</p>
        </div>
        
        <button type="submit" class="boton-confirmar">✅ Confirmar compra</button>
        <a href="vistaEvento.php?id=$idEvento" class="boton-cancelar">❌ Cancelar</a>
    </form>
</div>

<script src="js/compra.js"> </script> <!-- CORREGIDO: espacio antes de src -->
EOS;


require __DIR__.'/includes/vistas/plantillas/plantilla.php';
/*
if ($app->usuarioLogueado()) {
                if ($entradas > 0) {
                    $botonCompra = <<<EOS
                    <form action="compraEvento.php?id={$id}" method="POST" class="form-compra">
                        <div class="selector-cantidad">
                            <label>Cantidad: 
                                <input type="number" 
                                       name="cantidad" 
                                       min="1" 
                                       max="{$entradas}" 
                                       value="1"
                                       class="input-cantidad">
                            </label>
                        <div class="campo-puntos">
                            <label>Usar puntos (1 punto = 1€): 
                                <input type="number" name="puntos" 
                                    min="0" 
                                    max="{$usuario->getPuntos()}" 
                                    value="0">
                            </label>
                        <p class="info-puntos">Puntos disponibles: {$usuario->getPuntos()}</p>
                            </div>
                            <button type="submit" class="boton-accion comprar">🎟️ Comprar</button>
                        </div>
                    </form>
    EOS;

                } else {
                    $botonCompra = "<p class='aviso-agotado'>❌ No quedan entradas disponibles</p>";
                    
                }
                $botonCompra .= "<a href='$url' class='boton-accion foro'>💬 Foro del evento</a>";
            }
                */