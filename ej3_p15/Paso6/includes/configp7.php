<?php
session_start();

ini_set('default_charset', 'UTF-8');
setlocale(LC_ALL, 'es_ES.UTF.8');
date_default_timezone_set('Europe/Madrid');

// Autoload PSR-4
spl_autoload_register(function ($class) {
    $prefix = 'es\\ucm\\fdi\\aw\\';
    $base_dir = __DIR__ . '/clases/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require_once $file;
});

// Configuración de la aplicación (si existe)
if (file_exists(__DIR__.'/Aplicacion.php')) {
    $app = Aplicacion::getInstance();
    $app->init([
        'host' => 'localhost',
        'bd' => 'ejercicio3_db',
        'user' => 'usuario_admin',
        'pass' => 'adminpass'
    ]);
}
?>