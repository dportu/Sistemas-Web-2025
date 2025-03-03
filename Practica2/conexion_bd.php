<?php
    //CONEXION A BASE DE DATOS
    $host = "localhost"; 
    $usuario = "dani";  
    $clave = "gugugaga";
    $bd = "eventia_db";

    $conn = new mysqli($host, $usuario, $clave, $bd);
    // verificacion de errores en la conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

?>