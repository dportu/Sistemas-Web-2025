<?php
session_start();

$usuarios_validos = [
    "user" => [
        "password" => "userpass",
        "nombre" => "Usuario",
        "email" => "user@example.com" // Añadido email
    ],
    "admin" => [
        "password" => "adminpass",
        "nombre" => "Administrador",
        "email" => "admin@example.com", // Añadido email
        "esAdmin" => true
    ],
    "vip" => [
        "password" => "vippass",
        "nombre" => "VIP",
        "email" => "vip@example.com",
        "esVip" => true
    ]
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"] ?? '';
    $password = $_POST["password"] ?? '';

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
}

// Si llega aquí es por acceso incorrecto
header("Location: login.php");
exit;
?>