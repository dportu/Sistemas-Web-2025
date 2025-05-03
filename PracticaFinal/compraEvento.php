<?php
require_once __DIR__.'/includes/config.php';

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\eventos\Evento;
use es\ucm\fdi\aw\usuarios\Usuario;

$app = Aplicacion::getInstance();
$tituloPagina = 'Compra';

// Verificar usuario logueado
if (!$app->usuarioLogueado()) {
    header('Location: login.php');
    exit();
}

// Inicializar variables importantes
$mensaje = '';
$compraExitosa = false;
$id_evento = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
$puntos_usar = isset($_POST['puntos']) ? (int)$_POST['puntos'] : 0;

// Validaciones básicas
if ($id_evento <= 0 || $cantidad <= 0) {
    $app->paginaError(400, "Parámetros de compra inválidos");
    exit();
}

// Obtener objetos necesarios
$usuario = Usuario::buscaUsuario($app->nombreUsuario());
$evento = Evento::buscaPorId($id_evento);

// Verificar existencia de recursos
if (!$usuario || !$evento) {
    $app->paginaError(404, "Recurso no encontrado");
    exit();
}

// Lógica de compra
$precio_unitario = $evento->getPrecio();
$precio_total = $precio_unitario * $cantidad;
$descuento = min($puntos_usar, $usuario->getPuntos());
$precio_final = max(0, $precio_total - $descuento);
$nuevos_puntos = $usuario->getPuntos() - $descuento;

// Validar disponibilidad
if ($evento->getEntradasDisponibles() < $cantidad) {
    $mensaje = "❌ No hay suficientes entradas disponibles";
} elseif ($nuevos_puntos < 0) {
    $mensaje = "❌ No tienes suficientes puntos";
} else {
    // Realizar compra
    if ($evento->actualizaEntradas($cantidad) && $usuario->setPuntos($nuevos_puntos)) {
        $puntos_ganados = $precio_final * 0.5;
        $usuario->setPuntos( $puntos_ganados + $nuevos_puntos);

        Usuario::actualiza($usuario);
        $compraExitosa = true;


        $mensaje = "✅ Compra exitosa!<br>
                   - Entradas: $cantidad<br>
                   - Descuento: {$descuento}€<br>
                   - Total pagado: {$precio_final}€<br>
                   - Puntos ganados: {$puntos_ganados}<br>
                   - Puntos usados: {$puntos_usar}<br>
                   - Puntos restantes: {$nuevos_puntos}";

        // En compraEvento.php, después de validar la compra exitosa:
            if ($compraExitosa) {
                $conn = Aplicacion::getInstance()->getConexionBd();
                $query = sprintf(
                    "INSERT INTO compras (usuario, evento_id, cantidad, precio_unitario, puntos_usados) 
                    VALUES ('%s', %d, %d, %.2f, %d)",
                    $conn->real_escape_string($usuario->getUsername()),
                    $evento->getId(),
                    $cantidad,
                    $precio_unitario,
                    $descuento
                    );
                
                if ($conn->query($query)) {
                    // Éxito
                } else {
                    error_log("Error al guardar compra: " . $conn->error);
                }
            }
    } else {
        $mensaje = "❌ Error al procesar la compra";
}
}

// Mostrar resultado
$contenidoPrincipal = <<<EOS
<div class="resultado-compra">
    <h2>Resultado de la compra</h2>
    <p>$mensaje</p>
    <a href="vistaEvento.php?id=$id_evento" class="boton-volver">Volver al evento</a>
</div>
EOS;

require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>