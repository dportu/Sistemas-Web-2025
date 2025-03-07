<?php
include("conexion_bd.php");
session_start();
$pagina_act = basename($_SERVER['PHP_SELF']);

// Vemos si esta logeado
if(!isset($_SESSION['login']) || $_SESSION['login'] !== true || !isset($_SESSION['usuario_nombre']) || !isset($_SESSION['usuario_email'])) {
    echo "<script>alert('Debe iniciar sesión para publicar'); window.location=$pagina_act;</script>";
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
  $titulo = $_POST["titulo"];
  $mensaje = $_POST["mensaje"];
  $evento = $_POST["evento"];
  
  // Cogemos el autor y el gmail
  $autor = $_SESSION['usuario_nombre'];
  $email = $_SESSION['usuario_email'];
  //Preparamos la consulta de aql manejando los valores con valores null-

  if($evento === "") {
    $stmt = $conexion->prepare("INSERT INTO foro (titulo, autor, email, mensaje, evento) VALUES (?, ?, ?, ?, NULL)");
    $stmt->bind_param("ssss", $titulo, $autor, $email, $mensaje);
  } else {
    $stmt = $conexion->prepare("INSERT INTO foro (titulo, autor, email, mensaje, evento) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $titulo, $autor, $email, $mensaje, $evento);
  }

  if($stmt->execute()) {
      // cogemos el id del evento del formulario con el parametro evento

      $id_evento = isset($_POST['evento']) ? $_POST['evento'] : null;

      // Redirigimos al foro del evento solo si existe id_evento
      if ($id_evento) {
          echo "<script>alert('Mensaje enviado con éxito'); window.location = 'foro.php?id=$id_evento';</script>";
      } else {
          // Si no hay un id_evento, redirigimos al foro general
          echo "<script>alert('Mensaje enviado con éxito'); window.location = 'foro.php';</script>";
      }
  } else {
      echo "<script>alert('Error al enviar el mensaje: $conexion->error'); window.history.back();</script>";
  }

  $stmt->close();
  $conexion->close();
}
?>
