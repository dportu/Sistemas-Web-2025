
<?php 
	require_once __DIR__.'/includes/config.php';
    
	$tituloPagina = 'Moderar foro';

	$contenidoPrincipal = <<<EOS
        <p> Aquí se podrá moderar el foro </p>
    EOS;
	
	require __DIR__.'/includes/vistas/plantillas/plantilla.php';
  
?>