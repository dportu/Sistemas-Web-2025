<?php
session_start();
include 'conexion_bd.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["usuario"]);
    $password = trim($_POST["password"]);

    if (!empty($username) && !empty($password)) {
        // Preparar la consulta para evitar SQL Injection
        $sql = "SELECT username, email, password, rol FROM usuarios WHERE username = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 1) {
            $usuario_result = $resultado->fetch_assoc();
            // Verificar la contraseña
            if (password_verify($password, $usuario_result["password"])) {
                $_SESSION["usuario_nombre"] = $usuario_result["username"];
                $_SESSION["login"] = true;
                $_SESSION["usuario_email"] = $usuario_result["email"];
                $_SESSION["usuario_rol"] = $usuario_result["rol"];
                $_SESSION['puntos'] = $usuario_result["puntos"];
                header("Location: index.php");
                exit();
            } else {
                // Mensaje para contraseña incorrecta
                $_SESSION['error_login'] = "La contraseña ingresada es incorrecta.";
            }
        } else if ($resultado->num_rows == 0) {
            // Mensaje para usuario no encontrado
            $_SESSION['error_login'] = "El usuario no existe en nuestra base de datos.";
        } else {
            // Mensaje para error de múltiples usuarios
            $_SESSION['error_login'] = "Error en la base de datos: múltiples usuarios con el mismo nombre.";
        }

        $stmt->close();
    } else {
        // Mensaje para campos vacíos
        $_SESSION['error_login'] = "Por favor, completa todos los campos.";
    }
    
    // Redirección a la página de login
    header("Location: login.php");
    exit();
}

$conexion->close();
?>