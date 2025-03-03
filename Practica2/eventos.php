<?php
    include("conexion_bd.php");

    $resultado = $conexion->query("SELECT * FROM eventos");

    echo "<h2>Lista de Eventos</h2>";
    echo "<ul>";
    while ($fila = $resultado->fetch_assoc()) {
        $fecha_texto = $fila['fecha_inicio'];
        if (!empty($fila['fecha_fin'])) {
            $fecha_texto .= " al " . $fila['fecha_fin'];
        }
        
        echo "<li>
            <a href='evento.php?id=" . $fila['id'] . "'>
                <strong>" . $fila['nombre'] . "</strong>
            </a> - " . $fecha_texto . "<br>";
        
        echo "</li>";
    }
    echo "</ul>";
?>