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

                 //INICIO DE SESION AUTOMATICO cambiar a funcion publica?
                 $_SESSION["usuario_nombre"] = $usuario;
                 $_SESSION["login"] = true;
                 $_SESSION["usuario_email"] = $email;
                 $_SESSION["usuario_rol"] = 'cliente';
                 header("Location: index.php"); // Redirigir a la página de usuario
                
                
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