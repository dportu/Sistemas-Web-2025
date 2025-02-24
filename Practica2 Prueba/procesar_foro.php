<?php
include("conexion.php");

if($_SERVER["REQUEST_METHOD"] == "POST") {
  $titulo = $_POST["titulo"];
  $autor = $_POST["autor"];
  $email = $_POST["email"];
  $mensaje = $_POST["mensaje"];
  $evento =   $_POST["evento"];

    $stmt = $conexion->prepare("INSERT INTO foro (titulo, autor, email, mensaje, evento) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $titulo, $autor, $email, $mensaje, $evento);

    if($stmt->execute()) {
        echo "<script>alert('Mensaje enviado con éxito'); window.location='foro.php';</script>";
    } else {
        echo "<script>alert('Error al enviar el mensaje'); window.history.back();</script>";
    }

    $stmt->close();
    $conexion->close();
}