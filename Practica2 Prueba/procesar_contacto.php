<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $motivo = $_POST["motivo"];
    $consulta = $_POST["consulta"];

    $stmt = $conexion->prepare("INSERT INTO correos (nombre, email, motivo, consulta) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $email, $motivo, $consulta);

    if ($stmt->execute()) {
        echo "<script>alert('Mensaje enviado con éxito'); window.location='foro_correos.php';</script>";
    } else {
        echo "<script>alert('Error al enviar el mensaje'); window.history.back();</script>";
    }

    $stmt->close();
    $conexion->close();
}
?>
