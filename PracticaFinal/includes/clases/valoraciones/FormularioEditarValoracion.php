<?php
namespace es\ucm\fdi\aw\valoraciones;

use es\ucm\fdi\aw\Formulario;
use es\ucm\fdi\aw\Aplicacion;

class FormularioEditarValoracion extends Formulario {

    private $idValoracion;

    public function __construct($idValoracion) {
        parent::__construct('formEditarValoracion');
        $this->idValoracion = $idValoracion;
    }

    protected function generaCamposFormulario(&$datos) {
        
        $valoracion = Valoracion::getValoracionPorId($this->idValoracion);

        if (!$valoracion) {
            return "<p class='error'>No se encontró la valoración para editar.</p>";
        }

        $nota = $valoracion->getNota();
        $comentario = $valoracion->getComentario();

        return <<<EOF
            <h3>Edita tu valoración</h3>
            <label for="nota">Nota (1-5):</label>
            <input type="number" class="texto-card" id="nota" name="nota" min="1" max="5" value="$nota" required>

            <label for="comentario">Comentario:</label>
            <textarea id="comentario" class="texto-card" name="comentario" required>$comentario</textarea>

            <input type="submit" class="texto-card" value="Guardar cambios">
        EOF;
    }

    protected function procesaFormulario(&$datos) {
        $nota = trim($datos['nota'] ?? '');
        $comentario = trim($datos['comentario'] ?? '');

        if (empty($nota) || $nota < 1 || $nota > 5) {
            $this->errores['nota'] = 'La nota debe estar entre 1 y 5.';
        }

        $valoracion = Valoracion::getValoracionPorId($this->idValoracion); 

        if (!Valoracion::editarValoracion($this->idValoracion, $nota, $comentario)) {
            $this->errores[] = "No se pudo actualizar el mensaje.";
        }

        // Redirigir al foro del evento o al foro general
        $urlRedireccion = "vistaEvento.php?id=" . $valoracion->getIdEvento();
        header("Location: $urlRedireccion");
        exit(); 
        
    }
}
?>
