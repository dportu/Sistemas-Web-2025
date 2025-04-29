<?php
require_once __DIR__.'/includes/config.php';
use es\ucm\fdi\aw\admin\Admin;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $resultado = Admin::crearEvento($_POST);
        if ($resultado[0]) {
            $app->putAtributoPeticion('exito', $resultado[1]);
        } else {
            $app->putAtributoPeticion('error', $resultado[1]);
        }
    } catch (Exception $e) {
        $app->putAtributoPeticion('error', $e->getMessage());
    }
    header("Location: anyadir_evento.php");
}