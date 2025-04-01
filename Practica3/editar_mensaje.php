<?php
    /*
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
    */
?>