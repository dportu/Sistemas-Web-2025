<?php 

	require_once __DIR__.'/includes/config.php';
	use es\ucm\fdi\aw\eventos\Evento;

	$tituloPagina = 'Inicio';

	$contenidoPrincipal = '';

	// TODO: Mostrar los eventos que hay disponibles
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
	
	require __DIR__.'/includes/vistas/plantillas/plantilla.php';
  
?>