<?php

    require_once 'includes/config.php';

    
?>

<?php 
    use es\ucm\fdi\aw\Aplicacion;
    use es\ucm\fdi\aw\eventos\Evento;
    use es\ucm\fdi\aw\usuarios\Usuario;

	require_once __DIR__.'/includes/config.php';
    
	$tituloPagina = 'Compra';

    $app = Aplicacion::getInstance();
    $nombreUsuario = $app->nombreUsuario();
    //conseguimos el usuario
    $usuario = Usuario::buscaUsuario($nombreUsuario);


    $id_evento = isset($_GET['id']) ? (int)$_GET['id'] : null;
    $precio = Evento::buscaPorId($id_evento)->getPrecio();
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;

    $compraExitosa = Evento::compra($id_evento, $usuario, $precio, $cantidad ); //cantidad aun no implementada

    if ($compraExitosa) {
        $mensaje = "✅ Compra de $cantidad entradas realizada con éxito!";
        $mensaje .= "<br>Puntos acumulados: +" . ($precio * $cantidad / 2);
    } else {
        $mensaje = "❌ Error en la compra. Verifica la disponibilidad de entradas";
    }
	$contenidoPrincipal = <<<EOS
    <div class="resultado-compra">
        <h2>Resultado de la compra</h2>
        <p>$mensaje</p>
        <a href="vistaEvento.php?id=$id_evento" class="boton-volver">Volver al evento</a>
    </div>
    EOS;
        
	require __DIR__.'/includes/vistas/plantillas/plantilla.php';
  
?>
