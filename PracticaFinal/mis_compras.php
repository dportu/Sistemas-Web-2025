<?php
    require_once __DIR__.'/includes/config.php';

    use es\ucm\fdi\aw\Aplicacion;
    use es\ucm\fdi\aw\usuarios\Usuario;
    use es\ucm\fdi\aw\compras\Compra;
    use es\ucm\fdi\aw\eventos\Evento;

    $app = Aplicacion::getInstance();
    $tituloPagina = 'Mis Compras';

    function mostrarEntradas($username, &$contenidoPrincipal) {
        $entradas = Compra::getEntradasUsuario($username);
       // Mostrar todas las entradas del usuario
       $res = '<div id="contenedor-eventos">';
       foreach ($entradas as $idEvento => $numeroDeEntradas) {
            $evento = Evento::buscaPorId($idEvento);
        
            if ($evento) {
                $imagen = htmlspecialchars($evento->getImagen());
                $nombre = htmlspecialchars($evento->getNombre());
                $precio = $evento->getPrecio();
                $fecha = date('d/m/Y H:i', strtotime($evento->getFecha()));
        
                $res .= <<<EOS
                    <div class="evento">
                        <a href="vistaEvento.php?id={$evento->getId()}">
                            <img src="{$imagen}" alt="{$nombre}" class="evento-icono">
                            <h3>{$nombre}</h3>
                            <p>{$precio} €</p>
                            <p>{$fecha}</p>
                            <p>{$numeroDeEntradas} entradas adquiridas</p>
                        </a>
                    </div>
                EOS;
            }
        }
        if (empty($entradas)) {
            $res .= "<p class='aviso-compras'> Aún no has realizado ninguna compra</p>";
        }
        $res .= '</div>';

        return $res;
    }

    // Verificar usuario logueado
    if (!$app->usuarioLogueado()) {
        $app->redirige($app->buildUrl('login.php'));
    }

    // Mostrar el historial de compras
    $comprasHTML = mostrarEntradas($app->nombreUsuario(), $contenidoPrincipal);

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

