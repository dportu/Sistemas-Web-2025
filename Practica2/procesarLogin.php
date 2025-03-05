<?php
    session_start();
    require("conexion_bd.php");


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $usuario = trim($_POST["usuario"]);
        $password = trim($_POST["password"]);

        if (!empty($usuario) && !empty($password)) {
            // Preparar la consulta para evitar SQL Injection
            $sql = "SELECT username, password FROM usuarios WHERE username = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows == 1) {
                $usuario_result = $resultado->fetch_assoc();
                // Verificar la contraseña usando password_verify
               
                if (isset($usuarios_validos[$usuario]) && $password === $usuarios_validos[$usuario]["password"]) {
                    // Establecer todas las variables de sesión necesarias
                    $_SESSION["login"] = true;
                    $_SESSION["usuario_nombre"] = $usuarios_validos[$usuario]["nombre"];
                    $_SESSION["usuario_email"] = $usuarios_validos[$usuario]["email"];
                    
                    if (isset($usuarios_validos[$usuario]["esAdmin"])) {
                        $_SESSION["esAdmin"] = true;
                    }
                    
                    // Redirigir en lugar de mostrar HTML
                    header("Location: index.php");
                    exit;
                } else {
                    $_SESSION['error_login'] = "Usuario o contraseña incorrectos";
                    header("Location: login.php");
                    exit;
                }


                /*
                if (password_verify($password, $usuario_result["password"])) { // cuando pasemos a contraseñas encriptadas
                    $_SESSION["usuario_id"] = $usuario_result["id"];
                    $_SESSION["usuario_nombre"] = $usuario_result["username"];
                    $_SESSION["login"] = true;
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
        }*/
    }
?>