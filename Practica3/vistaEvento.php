<?php 
    namespace es\ucm\fdi\aw;
	use es\ucm\fdi\aw\eventos\Evento;

    require_once __DIR__.'/includes/config.php';

	$tituloPagina = 'Eventos';

    $contenidoPrincipal = '';
    mostrarEvento(null, $contenidoPrincipal);

    $usuarioAutenticado = isset($_SESSION["login"]) && $_SESSION["login"];
    $id_evento = $_GET['id'];

    function mostrarInfoEvento() {
        global $id_evento;
        $evento = Evento::buscaPorId($id_evento);

        echo "<h2>[ " . htmlspecialchars($evento['nombre']) . " ]</h2>";

        echo "<img src='" . htmlspecialchars($evento['imagen']) . "' alt='Imagen del evento'>";
        echo "<p><strong>Precio:</strong> " . $evento['precio'] . " €</p>";
        echo "<p><strong>Fecha:</strong> " . $evento['fecha_inicio'] . "</p>";

        echo !empty($evento['ubicacion']) ? "<p><strong>Ubicacion:</strong> " . htmlspecialchars($evento['ubicacion']) . "</p>" : "";
        echo !empty($evento['organizador']) ? "<p><strong>Organizador:</strong> " . htmlspecialchars($evento['organizador']) . "</p>" : "";
        echo !empty($evento['descripcion']) ? "<p><strong>Descripción:</strong> " . htmlspecialchars($evento['descripcion']) . "</p>" : "";
    }

    function mostrarEvento($id, &$contenidoPrincipal) {
        if($id == null) {
            $eventos = Evento::getEventos();
            for ($i = 0; $i < count($eventos); $i++) {
                $id = $eventos[$i]->id;
                $imagen = $eventos[$i]->imagen;
                $nombre = $eventos[$i]->nombre;
                $precio = $eventos[$i]->precio;
                $fecha = $eventos[$i]->fecha;

                $contenidoPrincipal .= <<<EOS
                    <a href="vistaEvento.php?id={$id}" class="evento-card">
                        <img src="{$imagen}" alt="Imagen de {$nombre}" class="evento-imagen">
                        <h3>[ {$nombre} ]</h3>
                        <p> {$precio} € </p>
                        <p> {$fecha} </p>
                    </a>
                EOS;
            }
        }

        else {
            $evento = Evento::buscaPorId($id);

            $imagen = $evento->imagen;
            $nombre = $evento->nombre;
            $precio = $evento->precio;
            $fecha = $evento->fecha;

            $contenidoPrincipal .= <<<EOS
                    <a href="vistaEvento.php?id={$id}" class="evento-card">
                        <img src="{$imagen}" alt="Imagen de {$nombre}" class="evento-imagen">
                        <h3>[ {$nombre} ]</h3>
                        <p> {$precio} € </p>
                        <p> {$fecha} </p>
                    </a>
                EOS;
        }
        
    }
	
	require __DIR__.'/includes/vistas/plantillas/plantilla.php';
  
?>