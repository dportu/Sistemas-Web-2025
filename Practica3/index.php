<?php 

	require_once __DIR__.'/includes/config.php';

	$tituloPagina = 'Inicio';

	$contenidoPrincipal = '';

	// TODO: Mostrar los eventos que hay disponibles
	
	while ($evento = $eventos->fetch_array(MYSQLI_ASSOC)) {
		$contenidoPrincipal .= '<div class="card mb-3">';
		$contenidoPrincipal .= '<div class="card-body">';
		$contenidoPrincipal .= '<h5 class="card-title">'.$evento['nombre'].'</h5>';
		$contenidoPrincipal .= '<p class="card-text">'.$evento['descripcion'].'</p>';
		$contenidoPrincipal .= '<p class="card-text"><small class="text-muted">Fecha: '.$evento['fecha'].'</small></p>';
		$contenidoPrincipal .= '</div>';
		$contenidoPrincipal .= '</div>';
	}

	require __DIR__.'/includes/vistas/plantillas/plantilla.php';
  
?>