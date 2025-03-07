<?php
include("conexion_bd.php");
session_start();

// Verificar si el usuario está logueado
if(!isset($_SESSION['login']) || $_SESSION['login'] !== true || !isset($_SESSION['usuario_nombre']) || !isset($_SESSION['usuario_email'])) {
    echo "<script>alert('Debe iniciar sesión para publicar'); window.location='login.php';</script>";
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
  $titulo = $_POST["titulo"];
  $mensaje = $_POST["mensaje"];
  $evento = $_POST["evento"];
  
  // Obtener autor y email directamente de la sesión
  $autor = $_SESSION['usuario_nombre'];
  $email = $_SESSION['usuario_email'];

  // Preparar la consulta SQL con manejo adecuado de valores NULL
  if($evento === "") {
    $stmt = $conexion->prepare("INSERT INTO foro (titulo, autor, email, mensaje, evento) VALUES (?, ?, ?, ?, NULL)");
    $stmt->bind_param("ssss", $titulo, $autor, $email, $mensaje);
  } else {
    $stmt = $conexion->prepare("INSERT INTO foro (titulo, autor, email, mensaje, evento) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $titulo, $autor, $email, $mensaje, $evento);
  }

  if($stmt->execute()) {
      echo "<script>alert('Mensaje enviado con éxito'); window.location='foro.php';</script>";
  } else {
      echo "<script>alert('Error al enviar el mensaje: " . $conexion->error . "'); window.history.back();</script>";
  }

  $stmt->close();
  $conexion->close();
}