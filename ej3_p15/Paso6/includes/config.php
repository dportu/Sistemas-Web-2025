<?php
session_start();

// Parámetros de BD
define('BD_HOST', 'localhost');
define('BD_NAME', 'ejercicio3_db');
define('BD_USER', 'usuario_cliente');
define('BD_PASS', 'clientepass');

// ... (definiciones de rutas)

define('RAIZ_APP', __DIR__);
define('RUTA_APP', '/ej3');
define('RUTA_IMGS', RUTA_APP.'img/');
define('RUTA_CSS', RUTA_APP.'css/');
define('RUTA_JS', RUTA_APP.'js/');



// Inicialización de la aplicación
require_once __DIR__.'/Aplicacion.php';
$app = Aplicacion::getInstance();
$app->init([
    'host' => BD_HOST,
    'bd' => BD_NAME,
    'user' => BD_USER,
    'pass' => BD_PASS
]);

register_shutdown_function([$app, 'shutdown']);