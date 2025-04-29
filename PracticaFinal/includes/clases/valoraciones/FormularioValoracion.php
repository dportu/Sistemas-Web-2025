<?php
namespace es\ucm\fdi\aw\valoraciones;

use es\ucm\fdi\aw\Formulario;
use es\ucm\fdi\aw\Aplicacion;

class FormularioValoracion extends Formulario {

    private $idEvento;

    public function __construct($idEvento) {
        parent::__construct('formValoracion');
        $this->idEvento = $idEvento;
    }

    protected function generaCamposFormulario(&$datos) {
        $nota = $datos['nota'] ?? '';
        $comentario = $datos['comentario'] ?? '';
        $erroresCampos = self::generaErroresCampos(['nota', 'comentario'], $this->errores, 'span', ['class' => 'error']);

        return <<<HTML
            <h3>Valora este evento</h3>
            <label for="nota">Nota (1-5):</label>
            <input type="number" id="nota" name="nota" min="1" max="5" value="$nota" required>
            {$erroresCampos['nota']}

            <label for="comentario">Comentario:</label>
            <textarea id="comentario" name="comentario" required>$comentario</textarea>
            {$erroresCampos['comentario']}

            <input type="submit" name="enviar" value="Enviar valoración">
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

        $app = Aplicacion::getInstance();
        if (!$app->usuarioLogueado()) {
            $this->errores[] = 'Debes iniciar sesión para valorar.';
            return;
        }

        $usuario = $app->nombreUsuario();

        if (count($this->errores) === 0) {
            $conn = $app->getConexionBd();
            $stmt = $conn->prepare("INSERT INTO valoraciones (id_evento, username, nota, comentario, fecha) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("isis", $this->idEvento, $usuario, $nota, $comentario);
            if (!$stmt->execute()) {
                $this->errores[] = "Error al insertar la valoración.";
            }
            $stmt->close();
        }
    }
}
?>
