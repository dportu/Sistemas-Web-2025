<?php

    function getEvento($id_evento) {
        include("conexion_bd.php");
            
        $stmt = $conexion->prepare("SELECT * FROM eventos WHERE id = ?");
        $stmt->bind_param("i", $id_evento);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $evento = $resultado->fetch_assoc();

        $stmt->close();

        return $evento; // array asociativo
    }
?>