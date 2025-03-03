<?php
include("conexion.php");
session_start();

// Verificar si el usuario está logueado
if(!isset($_SESSION['usuario_nombre']) || !isset($_SESSION['usuario_email'])) {
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

  // Preparar la consulta SQL
  $stmt = $conexion->prepare("INSERT INTO foro (titulo, autor, email, mensaje, evento) VALUES (?, ?, ?, ?, ?)");   // esto es para evitar inyecciones SQL
  $stmt->bind_param("sssss", $titulo, $autor, $email, $mensaje, $evento);

  if($stmt->execute()) {
      echo "<script>alert('Mensaje enviado con éxito'); window.location='foro.php';</script>";
  } else {
      echo "<script>alert('Error al enviar el mensaje: " . $conexion->error . "'); window.history.back();</script>";
  }

  $stmt->close();
  $conexion->close();
}