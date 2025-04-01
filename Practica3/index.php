<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Handjet:wght@100..900&display=swap" rel="stylesheet">
<?php 
	use es\ucm\fdi\aw\eventos\Evento;
	require_once __DIR__.'/includes/config.php';
    
	$tituloPagina = 'Inicio';

	$contenidoPrincipal = '';
	mostrarEventos($contenidoPrincipal);

	function mostrarEventos(&$contenidoPrincipal) {
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
	
	require __DIR__.'/includes/vistas/plantillas/plantilla.php';
  
?>