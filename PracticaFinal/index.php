

<?php 
	use es\ucm\fdi\aw\eventos\Evento;
	require_once __DIR__.'/includes/config.php';
    
	$tituloPagina = 'Inicio';

	$contenidoPrincipal = '';
	mostrarEventos($contenidoPrincipal);

	function mostrarEventos(&$contenidoPrincipal) {
        $eventos = Evento::getEventos();

        $contenidoPrincipal .= '<div id="contenedor-eventos">';

            for ($i = 0; $i < count($eventos); $i++) {
                $id = $eventos[$i]->id;
                $imagen = $eventos[$i]->imagen;
                $nombre = $eventos[$i]->nombre;
                $precio = $eventos[$i]->precio;
                $fecha = $eventos[$i]->fecha;

                $contenidoPrincipal .= <<<EOS
                    <div class="evento">
                        <a href="vistaEvento.php?id={$id}">
                            <img src="{$imagen}" alt="Imagen de {$nombre}" class="evento-icono">
                            <h3>[ {$nombre} ]</h3>
                            <p> {$precio} € </p>
                            <p> {$fecha} </p>
                        </a>
                    </div>
                EOS;
            }
            $contenidoPrincipal .= '</div>';
    }
	
	require __DIR__.'/includes/vistas/plantillas/plantilla.php';
  
?>