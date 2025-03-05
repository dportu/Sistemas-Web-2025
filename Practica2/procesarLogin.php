<?php
session_start();
include 'conexion_bd.php';

//DANDO POR HECHO QUE USUARIO ES nombre EN LA BASE DE DATOS

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["usuario"]);
    $password = trim($_POST["password"]);

    if (!empty($usuario) && !empty($password)) {
        // Preparar la consulta para evitar SQL Injection
        $sql = "SELECT username, email, password FROM usuarios WHERE username = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 1) {
            $usuario_result = $resultado->fetch_assoc();
            // Verificar la contraseña usando password_verify
            if ($password == $usuario_result["password"]) { //password_verify($password, $usuario_result["password"]) cuando pasemos a contraseñas encriptadas
                $_SESSION["usuario_nombre"] = $usuario_result["username"];
                $_SESSION["login"] = true;
                $_SESSION["usuario_email"] = $usuario_result["email"];
                header("Location: index.php"); // Redirigir a la página de usuario
                exit();
            } else {
                echo "Contraseña incorrecta.";
            }
        } 
        else if ($resultado->num_rows == 0){
            echo "Usuario no encontrado.";
        }
        else {
            echo "ERROR DE BASE DE DATOS, MULTIPLES USUARIOS CON EL MISMO NOMBRE";
        }

        $stmt->close();
    } else {
        echo "Por favor, completa todos los campos.";
    }
}

$conn->close();
?>