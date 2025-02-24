<!DOCTYPE html>
<html lang='es'>

<head>
    <meta charset="utf-8">
    <title>Contacto Eventia</title>
    <link id="estilo" rel="stylesheet" type="text/css" href="CSS/default.css"/>
</head>

<body>
    <p> Lista de enlaces: 
    <a href= "index.php">indice</a>,
        <a href= "detalles.html">detalles</a>, 
        <a href= "bocetos.html">bocetos</a>, 
        <a href= "miembros.html">miembros</a>, 
        <a href= "planificacion.html">planificacion</a>, 
    </p>

    <h1>Formulario de Contacto</h1>
    <form action="procesar_contacto.php" method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>
        <br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br><br>

        <p>Motivo de la Consulta:</p>
        <input type="radio" id="evaluacion" name="motivo" value="Evaluacion" required>
        <label for="evaluacion">Evaluación</label>  
        <br>
        <input type="radio" id="sugerencias" name="motivo" value="Sugerencias" required>
        <label for="sugerencias">Sugerencias</label>
        <br>
        <input type="radio" id="criticas" name="motivo" value="Criticas" required>
        <label for="criticas">Críticas</label>
        <br><br>

        <label for="consulta">Consulta:</label>
        <br>
        <textarea name="consulta" rows="10" cols="50" required></textarea>
        <br><br>

        <input type="checkbox" id="terminos" name="terminos" required>
        <label for="terminos">Marque esta casilla para verificar que ha leído nuestros términos y condiciones.</label>
        <br><br>

        <input type="submit" value="Enviar">
    </form>
</body>
</html>
