<?php
require_once __DIR__.'/includes/config.php';

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\usuarios\Usuario;

$app = Aplicacion::getInstance();
$tituloPagina = 'Mis Compras';

// Verificar usuario logueado
if (!$app->usuarioLogueado()) {
    $app->redirige($app->buildUrl('login.php'));
}

// Obtener usuario y compras
$usuario = Usuario::buscaUsuario($app->nombreUsuario());
$comprasHTML = '';

if ($usuario) {
    $compras = Usuario::getComprasByUsuario($usuario->getUsername());
    
    foreach ($compras as $compra) {
        $fecha = date('d/m/Y H:i', strtotime($compra['fecha_compra']));
        $total = $compra['precio_unitario'] * $compra['cantidad'];
        
        $comprasHTML .= <<<EOS
        <div class="compra-item">
            <img src="{$compra['evento_imagen']}" class="compra-imagen" alt="{$compra['evento_nombre']}">
            <div class="compra-detalle">
                <h3>{$compra['evento_nombre']}</h3>
                <p><strong>Fecha:</strong> {$fecha}</p>
                <p><strong>Cantidad:</strong> {$compra['cantidad']} entradas</p>
                <p><strong>Precio unitario:</strong> {$compra['precio_unitario']}€</p>
                <p><strong>Total:</strong> {$total}€</p>
                <p><strong>Puntos usados:</strong> {$compra['puntos_usados']}</p>
            </div>
        </div>
        EOS;
    }
    
    if (empty($compras)) {
        $comprasHTML = "<p class='aviso-compras'>🎫 Aún no has realizado ninguna compra</p>";
    }
}

$contenidoPrincipal = <<<EOS
    <div class="contenedor-compras">
        <h1>Historial de Compras</h1>
        <div class="lista-compras">
            {$comprasHTML}
        </div>
        <a href="perfil.php" class="boton-volver">← Volver al perfil</a>
    </div>
EOS;

require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>