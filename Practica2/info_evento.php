<?php

    function getEvento($id_evento) {
        include("conexion_bd.php");
            
        $stmt = $conexion->prepare("SELECT * FROM eventos WHERE id = ?");
        $stmt->bind_param("i", $id_evento);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $evento = $resultado->fetch_assoc();

        $stmt->close();

        return $evento; 
    }

    function getValoraciones($id_evento) {
        include("conexion_bd.php");
    
        $stmt = $conexion->prepare("SELECT * FROM valoraciones WHERE id_evento = ?");
        $stmt->bind_param("i", $id_evento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $valoraciones = [];
        while ($fila = $resultado->fetch_assoc()) {
            $valoraciones[] = $fila;
        }
    
        $stmt->close();
        
        return $valoraciones;
    }
?>