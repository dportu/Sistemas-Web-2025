<?php
    session_start();
    require("conexion_bd.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $usuario = trim($_POST["usuario"]);
        $password = trim($_POST["password"]);
        //$password = password_hash($password, PASSWORD_DEFAULT); //encriptamos la contraseña
        $email = trim($_POST["email"]);

        //FALTA COMPROBAR QUE EL USUARIO NO ESTE REGISTRADO YA
        if (!empty($usuario) && !empty($password) && !empty($email)) {
            $sql = "SELECT username, password FROM usuarios WHERE username = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows == 0) {
                $usuario_result = $resultado->fetch_assoc();
                 // Preparar la consulta para evitar SQL Injection
                 $sql = "INSERT INTO usuarios (username, password, email)values (?, ?, ?)";
                 $stmt = $conexion->prepare($sql);
                 $stmt->bind_param("sss", $usuario, $password, $email);
                 $stmt->execute(); 

                 //inicio de sesion
                 $_SESSION["usuario_nombre"] = $usuario_result["username"];
                $_SESSION["login"] = true;
                header("Location: index.php");
                
            }
            else if ($resultado->num_rows > 0){
                echo "YA EXISTE UN USUARIO CON EL NOMBRE DE USUARIO ELEGIDO";
            }
            else {
                echo "Por favor, completa todos los campos.";
            } 
        }
    }
    $conexion->close();
    
?>