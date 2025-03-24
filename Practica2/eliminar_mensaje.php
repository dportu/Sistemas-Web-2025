<?php
include("conexion_bd.php");  
session_start();

if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    echo "<script>alert('Debe iniciar sesión para eliminar mensajes'); window.location='login.php';</script>";
    exit();
}

if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_mensaje = $_GET['id'];
    
    //Vemos si pertenece al autor
    $stmt = $conexion->prepare("SELECT autor FROM foro WHERE id = ?");
    $stmt->bind_param("i", $id_mensaje);
    
    try {
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if($resultado->num_rows === 1) {
            $fila = $resultado->fetch_assoc();
            
            // Comprobar si el usuario actual es el autor del mensaje
            if($fila['autor'] === $_SESSION['usuario_nombre']) {
                // Eliminar el mensaje
                $stmt = $conexion->prepare("DELETE FROM foro WHERE id = ?");
                $stmt->bind_param("i", $id_mensaje);
                
                try {
                    if($stmt->execute()) {
                        // Redirigir a la página de donde vino el usuario (usando HTTP_REFERER)
                        echo "<script>alert('Mensaje eliminado con éxito'); window.location='" . $_SERVER['HTTP_REFERER'] . "';</script>";
                    } else {
                        throw new Exception($conexion->error);
                    }
                } catch (Exception $e) {
                    echo "<script>alert('Error al eliminar el mensaje: " . $e->getMessage() . "'); window.location='" . $_SERVER['HTTP_REFERER'] . "';</script>";
                }
            } else {
                echo "<script>alert('No tienes permiso para eliminar este mensaje'); window.location='" . $_SERVER['HTTP_REFERER'] . "';</script>";
            }
        } else {
            echo "<script>alert('El mensaje no existe'); window.location='" . $_SERVER['HTTP_REFERER'] . "';</script>";
        }
    } catch (Exception $e) {
        echo "<script>alert('Error al verificar el mensaje: " . $e->getMessage() . "'); window.location='" . $_SERVER['HTTP_REFERER'] . "';</script>";
    }
    
    $stmt->close();
} else {
    echo "<script>alert('ID de mensaje no válido'); window.location='" . $_SERVER['HTTP_REFERER'] . "';</script>";
}

$conexion->close();
