
<?php 
	require_once __DIR__.'/includes/config.php';
    
	$tituloPagina = 'Inicio';

	$contenidoPrincipal = <<<EOS
        <p> Aquí se podrá ver las entradas del usuario </p>
    EOS;
	
	require __DIR__.'/includes/vistas/plantillas/plantilla.php';
  
?>