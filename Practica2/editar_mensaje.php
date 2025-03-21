<?php
include("conexion_bd.php");
session_start();


if(!isset($_SESSION['login']) || $_SESSION['login'] !== true || !isset($_SESSION['usuario_nombre'])) {
    echo "<script>alert('Debe iniciar sesión para editar mensajes'); window.location='login.php';</script>";
    exit();
}

$mensaje = [];
$error = '';

if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_mensaje = $_GET['id'];
    

    $stmt = $conexion->prepare("SELECT f.*, e.nombre AS nombre_evento FROM foro f LEFT JOIN eventos e ON f.evento = e.id WHERE f.id = ?");  // Con esto cogemos el mensaje y vemos si es del usuario actual
    $stmt->bind_param("i", $id_mensaje);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if($resultado->num_rows === 1) {
        $mensaje = $resultado->fetch_assoc();
        
      
        if($mensaje['autor'] !== $_SESSION['usuario_nombre']) {    // Se comprueba si es el autor del mensaje
            echo "<script>alert('No tienes permiso para editar este mensaje'); window.location='foro.php';</script>";   
            exit();
        }
    } else {
        echo "<script>alert('El mensaje no existe'); window.location='foro.php';</script>";
        exit();
    }
    
    $stmt->close();
} else {
    echo "<script>alert('ID de mensaje no válido'); window.location='foro.php';</script>";
    exit();
}


if($_SERVER["REQUEST_METHOD"] == "POST") {  // Procesamos el formulario al enviarl
    $titulo = $_POST["titulo"];
    $mensaje_contenido = $_POST["mensaje"];
    $evento = $_POST["evento"];
    
    
    if($evento === "") {   // Se actualiza el mensaje manejado con los adecuados valores de NULL
        $stmt = $conexion->prepare("UPDATE foro SET titulo = ?, mensaje = ?, evento = NULL WHERE id = ? AND autor = ?");
        $stmt->bind_param("ssis", $titulo, $mensaje_contenido, $id_mensaje, $_SESSION['usuario_nombre']);
    } else {
        $stmt = $conexion->prepare("UPDATE foro SET titulo = ?, mensaje = ?, evento = ? WHERE id = ? AND autor = ?");
        $stmt->bind_param("ssiis", $titulo, $mensaje_contenido, $evento, $id_mensaje, $_SESSION['usuario_nombre']);
    }
    
    if($stmt->execute()) {
        
        // Lo redirigimos si vemos que esta en un evento o en el general 
        if ($mensaje['evento'] !== null) {
            echo "<script>alert('Mensaje actualizado con éxito'); window.location = 'foro.php?id=" . $mensaje['evento'] . "';</script>";
        } else {
            echo "<script>alert('Mensaje actualizado con éxito'); window.location = 'foro.php';</script>";
        }
    } else {
        $error = "Error al actualizar el mensaje: " . $conexion->error;
    }
    
    
    $stmt->close();
}

// Obtener listado de eventos para el formulario
$eventos = [];
$query = "SELECT id, nombre FROM eventos ORDER BY fecha_inicio DESC";
$resultado_eventos = $conexion->query($query);
while($row = $resultado_eventos->fetch_assoc()) {
    $eventos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Editar Mensaje - Eventia</title>
    <link id="estilo" rel="stylesheet" type="text/css" href="CSS/estilo.css">
</head>

<body>
    <div id="contenedor">

    <?php require("includes/vistas/comun/cabecera.php"); ?>

    <?php require("includes/vistas/comun/sidebarIzq.php"); ?>

    <main>
        <h1>Editar Mensaje</h1>
        
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form id="editar-mensaje" action="editar_mensaje.php?id=<?php echo $id_mensaje; ?>" method="post">
           <label for="titulo">Título:</label>
           <input type="text" name="titulo" id="titulo" value="<?php echo htmlspecialchars($mensaje['titulo']); ?>" required><br><br>
           
           <p>Autor: <strong><?php echo htmlspecialchars($mensaje['autor']); ?></strong></p>
           <p>Email: <strong><?php echo htmlspecialchars($mensaje['email']); ?></strong></p>
           
            <?php if ($mensaje['evento']): ?>
                <p>Evento: <?php echo htmlspecialchars($mensaje['nombre_evento']); ?></p>
            <?php else: ?>
                <p>Evento: No asignado a ningún evento.</p>
            <?php endif; ?>
           
           <label for="mensaje">Mensaje:</label>
           <textarea name="mensaje" id="mensaje" rows="4" required><?php echo htmlspecialchars($mensaje['mensaje']); ?></textarea>
           
           <p><small>Fecha de publicación original: <?php echo $mensaje['fecha_publicacion']; ?></small></p>

           <input type="submit" value="Guardar Cambios">
           <a href="foro.php" style="margin-left: 10px;">Cancelar</a>
        </form>
    </main>

    <?php require("includes/vistas/comun/sidebarDer.php"); ?>
    <?php require("includes/vistas/comun/pie.php"); ?>
   
    <?php $conexion->close(); ?>
    </div>
</body>
</html>