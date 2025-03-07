<?php
    include("conexion_bd.php");

    $resultado = $conexion->query("SELECT * FROM eventos");

    echo "<h2>Lista de Eventos</h2>";
    echo "<div>";
    while ($fila = $resultado->fetch_assoc()) {
        $fecha_texto = $fila['fecha_inicio'];
        if (!empty($fila['fecha_fin'])) {
            $fecha_texto .= " al " . $fila['fecha_fin'];
        }
        
        $imagen = $fila['imagen'];
        $precio = $fila['precio'];
    
        echo "<a href='evento.php?id=" . $fila['id'] . "' class='evento-card'>
                <img src='$imagen' alt='Imagen de " . htmlspecialchars($fila['nombre']) . "' class='evento-imagen'>
                <h3>[ " . htmlspecialchars($fila['nombre']) . " ]</h3>
                <p> $precio € </p>
                <p> $fecha_texto </p>
             </a>";
    }
    echo "</div>";
?>