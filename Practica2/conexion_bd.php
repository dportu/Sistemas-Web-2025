<?php
    require_once 'config.php';

    $conexion = new mysqli(BD_HOST, BD_USER, BD_PASS, BD_NAME);

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }
?>



