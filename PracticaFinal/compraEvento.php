<?php
require_once __DIR__.'/includes/config.php';

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\eventos\Evento;
use es\ucm\fdi\aw\usuarios\Usuario;

$app = Aplicacion::getInstance();
$tituloPagina = 'Compra';
$mensaje = '';
$compraExitosa = false;

// Validación inicial
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$app->usuarioLogueado()) {
    header('Location: login.php');
    exit();
}

// Recoger parámetros
$id_evento = isset($_POST['id_evento']) ? (int)$_POST['id_evento'] : 0;
$cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
$puntos_usar = isset($_POST['puntos']) ? (int)$_POST['puntos'] : 0;

// Validar parámetros básicos
if ($id_evento <= 0 || $cantidad <= 0) {
    $app->paginaError(400, "Parámetros de compra inválidos");
    exit();
}

// Obtener objetos
$usuario = Usuario::buscaUsuario($app->nombreUsuario());
$evento = Evento::buscaPorId($id_evento);

// Verificar existencia
if (!$usuario || !$evento) {
    $app->paginaError(404, "Recurso no encontrado");
    exit();
}

// Validar disponibilidad y puntos
$errores = [];
if ($evento->getEntradasDisponibles() < $cantidad) {
    $errores[] = "No hay suficientes entradas disponibles";
}

if ($puntos_usar > $usuario->getPuntos()) {
    $errores[] = "No tienes suficientes puntos";
}

// Manejar errores
if (!empty($errores)) {
    $app->putAtributoPeticion('errores_compra', $errores);
    header("Location: procesar_compra.php?id=$id_evento");
    exit();
}

// Lógica de compra (manteniendo tu implementación original)
$precio_unitario = $evento->getPrecio();
$precio_total = $precio_unitario * $cantidad;
$descuento = min($puntos_usar, $usuario->getPuntos());
$precio_final = max(0, $precio_total - $descuento);
$nuevos_puntos = $usuario->getPuntos() - $descuento;

// Ejecutar transacción
if ($evento->actualizaEntradas($cantidad) && $usuario->setPuntos($nuevos_puntos)) {
    $puntos_ganados = $precio_final * 0.5;
    $usuario->setPuntos($puntos_ganados + $nuevos_puntos);
    Usuario::actualiza($usuario);
    $compraExitosa = true;

    // Registrar en BD (tu código original)
    $conn = $app->getConexionBd();
    $query = sprintf(
        "INSERT INTO compras (usuario, evento_id, cantidad, precio_unitario, puntos_usados) 
        VALUES ('%s', %d, %d, %.2f, %d)",
        $conn->real_escape_string($usuario->getUsername()),
        $evento->getId(),
        $cantidad,
        $precio_unitario,
        $descuento
    );
    
    if (!$conn->query($query)) {
        error_log("Error BD: " . $conn->error);
        $compraExitosa = false;
    }
}

// Mensaje final (tu formato original)
if ($compraExitosa) {
    $mensaje = "✅ Compra exitosa!<br>
               - Entradas: $cantidad<br>
               - Descuento: {$descuento}€<br>
               - Total pagado: {$precio_final}€<br>
               - Puntos ganados: {$puntos_ganados}<br>
               - Puntos usados: {$puntos_usar}<br>
               - Puntos restantes: " . $usuario->getPuntos();
} else {
    $mensaje = "❌ Error al procesar la compra";
}

// Vista (manteniendo tu estructura)
$contenidoPrincipal = <<<EOS
<div class="resultado-compra">
    <h2>Resultado de la compra</h2>
    <p>$mensaje</p>
    <a href="vistaEvento.php?id=$id_evento" class="boton-volver">Volver al evento</a>
    <a href="misEntradas.php" class="boton">Ver mis entradas</a>
</div>
EOS;

require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>