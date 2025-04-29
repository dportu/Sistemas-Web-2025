<?php
namespace es\ucm\fdi\aw\valoraciones;

use es\ucm\fdi\aw\Formulario;
use es\ucm\fdi\aw\Aplicacion;

class FormularioEditarValoracion extends Formulario {

    private $idEvento;
    private $usuario;

    public function __construct($idEvento, $usuario) {
        parent::__construct('formEditarValoracion', ['urlRedireccion' => "evento.php?id=$idEvento"]);
        $this->idEvento = $idEvento;
        $this->usuario = $usuario;
    }

    protected function generaCamposFormulario(&$datos) {
        // Obtener la valoración del usuario
        $conexion = Aplicacion::getInstance()->getConexionBd();
        $stmt = $conexion->prepare("SELECT * FROM valoraciones WHERE id_evento = ? AND username = ?");
        $stmt->bind_param("is", $this->idEvento, $this->usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $valoracion = $result->fetch_assoc();
        $stmt->close();

        if (!$valoracion) {
            return "<p class='error'>No se encontró la valoración para editar.</p>";
        }

        $nota = $valoracion['nota'];
        $comentario = $valoracion['comentario'];

        $erroresCampos = self::generaErroresCampos(['nota', 'comentario'], $this->errores, 'span', ['class' => 'error']);

        return <<<HTML
            <h3>Edita tu valoración</h3>
            <label for="nota">Nota (1-5):</label>
            <input type="number" id="nota" name="nota" min="1" max="5" value="$nota" required>
            {$erroresCampos['nota']}

            <label for="comentario">Comentario:</label>
            <textarea id="comentario" name="comentario" required>$comentario</textarea>
            {$erroresCampos['comentario']}

            <input type="submit" value="Guardar cambios">
        HTML;
    }

    protected function procesaFormulario(&$datos) {
        $nota = trim($datos['nota'] ?? '');
        $comentario = trim($datos['comentario'] ?? '');

        if (empty($nota) || $nota < 1 || $nota > 5) {
            $this->errores['nota'] = 'La nota debe estar entre 1 y 5.';
        }
        if (empty($comentario)) {
            $this->errores['comentario'] = 'El comentario no puede estar vacío.';
        }

        if (count($this->errores) === 0) {
            $conexion = Aplicacion::getInstance()->getConexionBd();
            $stmt = $conexion->prepare("UPDATE valoraciones SET nota = ?, comentario = ?, fecha = NOW() WHERE id_evento = ? AND username = ?");
            $stmt->bind_param("isis", $nota, $comentario, $this->idEvento, $this->usuario);
            if (!$stmt->execute()) {
                $this->errores[] = "No se pudo actualizar la valoración.";
            }
            $stmt->close();
        }
    }
}
?>
