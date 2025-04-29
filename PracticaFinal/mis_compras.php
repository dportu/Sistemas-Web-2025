
<?php 
	require_once __DIR__.'/includes/config.php';
    
	$tituloPagina = 'Inicio';

	$contenidoPrincipal = <<<EOS
        <p> Aquí aparecerán las compras de los usuarios </p>
    EOS;
	
	require __DIR__.'/includes/vistas/plantillas/plantilla.php';
  
?>