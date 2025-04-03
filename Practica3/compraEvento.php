<?php
    use es\ucm\fdi\aw\Aplicacion;
    use es\ucm\fdi\aw\eventos\Evento;
    use es\ucm\fdi\aw\usuarios\Usuario;

    require_once 'includes/config.php';

    $app = Aplicacion::getInstance();
    $nombreUsuario = $app->nombreUsuario();
    //conseguimos el usuario
    $usuario = Usuario::buscaUsuario($nombreUsuario);
    $id_evento = isset($_GET['id']) ? (int)$_GET['id'] : null;
    $precio = Evento::buscaPorId($id_evento)->getPrecio();

    $compraExitosa = Evento::compra($id_evento, $usuario, $precio, 0); //cantidad aun no implementada

    if ($compraExitosa) {
        echo "<p>Compra realizada con éxito. ¡Disfruta el evento!</p>";
    } else {
        echo "<p>Error al realizar la compra. Inténtalo de nuevo.</p>";
    }
?>
