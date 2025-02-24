<?php
include("conexion.php");

$resultado = $conexion->query("SELECT * FROM eventos");

echo "<h2>Lista de Eventos</h2>";
echo "<ul>";
while ($fila = $resultado->fetch_assoc()) {
    echo "<li><strong>" . $fila['nombre'] . "</strong> - " . $fila['fecha'] . "<br>" . $fila['descripcion'] . "</li>";
}
echo "</ul>";
?>
