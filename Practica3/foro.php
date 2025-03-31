<?php 

	require_once __DIR__.'/includes/config.php';

	use es\ucm\fdi\aw\foro\mensajeForo;

	$tituloPagina = 'Foro';

	$contenidoPrincipal = '';

    // Mostrar el foro dependiendo de su categoría
	$mensajes = mensajeForo::getMensajes($_GET['id']);
	for ($i = 0; $i < count($mensajes); $i++) {
		/*$id = $eventos[$i]->id;
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
		EOS;*/
	}

	require __DIR__.'/includes/vistas/plantillas/plantilla.php';
  
?>