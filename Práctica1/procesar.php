<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $nombre = htmlspecialchars($_POST["Nombre"]);
    $email = htmlspecialchars($_POST["email"]);
    $motivo = htmlspecialchars($_POST["motivo"]);
    $consulta = htmlspecialchars($_POST["consulta"]);

    // Formatear los datos
    $datos = "Fecha: " . date("Y-m-d H:i:s") . "\n";
    $datos .= "Nombre: $nombre\n";
    $datos .= "Email: $email\n";
    $datos .= "Motivo: $motivo\n";
    $datos .= "Consulta: $consulta\n";
    $datos .= "--------------------------------------\n";

    // Guardar en un archivo
    $archivo = "consultas.txt";
    if (file_put_contents($archivo, $datos, FILE_APPEND | LOCK_EX)) {
        echo "Gracias por tu mensaje, $nombre. Tu consulta ha sido enviada correctamente.";
    } else {
        echo "Error al guardar los datos. Intenta nuevamente.";
    }

    $destinatario = "parlandi@ucm.es";  // Dirección de correo a la que se enviarán las consultas
    $asunto = "Nueva consulta recibida";

    $mensaje = "Se ha recibido una nueva consulta:\n\n";
    $mensaje .= "Fecha: " . date("Y-m-d H:i:s") . "\n";
    $mensaje .= "Nombre: $nombre\n";
    $mensaje .= "Email: $email\n";
    $mensaje .= "Motivo: $motivo\n";
    $mensaje .= "Consulta:\n$consulta\n";

    $headers = "From: no-reply@tudominio.com" . "\r\n" .
               "Reply-To: $email" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();

    if (mail($destinatario, $asunto, $mensaje, $headers)) {
                // No es necesario mostrar un mensaje adicional ya que ya se está enviando un mensaje de éxito
    } else {
       echo "Hubo un problema al enviar el correo electrónico.";
    }


} else {
    echo "Error: Método no permitido.";
}
?>