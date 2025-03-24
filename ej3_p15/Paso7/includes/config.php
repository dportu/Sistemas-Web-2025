<?php
session_start();

// Configuración de autoload PSR-4


spl_autoload_register(function ($class) {
    $prefix = 'es\\ucm\\fdi\\aw\\'; 
    $base_dir = __DIR__ . '/clases/'; // Ruta desde includes/
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

// Parámetros de BD
define('BD_HOST', 'localhost');
define('BD_NAME', 'ejercicio3_db');
define('BD_USER', 'usuario_cliente');
define('BD_PASS', 'clientepass');

// Rutas
define('RAIZ_APP', __DIR__);
define('RUTA_APP', '/ej3');
define('RUTA_IMGS', RUTA_APP.'/img/');
define('RUTA_CSS', RUTA_APP.'/css/');
define('RUTA_JS', RUTA_APP.'/js/');

// Inicializar aplicación (solo una vez)
$app = \es\ucm\fdi\aw\Aplicacion::getInstance();
$app->init([
    'host' => BD_HOST,
    'bd' => BD_NAME,
    'user' => BD_USER,
    'pass' => BD_PASS
]);

// Registrar shutdown (solo una vez)
register_shutdown_function([$app, 'shutdown']);
?>