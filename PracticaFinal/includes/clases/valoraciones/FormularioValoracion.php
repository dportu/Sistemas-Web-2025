<?php
namespace es\ucm\fdi\aw\valoraciones;

use es\ucm\fdi\aw\Formulario;
use es\ucm\fdi\aw\Aplicacion;

class FormularioValoracion extends Formulario {

    private $id_evento;

    public function __construct($id_evento) {
        $this->id_evento = $id_evento;
        parent::__construct('formValoracion');
    }

    protected function generaCamposFormulario(&$datos) {
        $nota = $datos['nota'] ?? '';
        $comentario = $datos['comentario'] ?? '';

        $erroresCampos = self::generaErroresCampos(['nota', 'comentario'], $this->errores, 'span', ['class' => 'error']);
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);

        if (Aplicacion::getInstance()->usuarioLogueado()){
            $html = <<<EOF
                $htmlErroresGlobales
                <h3>Valora este evento</h3>
                <label for="nota">Nota (1-5):</label>
                <input type="number" id="nota" name="nota" min="1" max="5" value="$nota" required>
                {$erroresCampos['nota']}

                <label for="comentario">Comentario:</label>
                <textarea id="comentario" name="comentario" required>$comentario</textarea>
                {$erroresCampos['comentario']}

                <input type="submit" name="enviar" value="Enviar valoración" class="boton valoracion">
            EOF;
        }
        else {
            $html = <<<EOF
                $htmlErroresGlobales
                    <p>Para publicar una valoracion, debes <a href='login.php'> inicia sesión</a>.</p>
                EOF;
        }

        return $html;
    }

    protected function procesaFormulario(&$datos) {
        
        $nota = trim($datos['nota'] ?? '');
        $comentario = trim($datos['comentario'] ?? '');

        if (empty($nota) || $nota < 1 || $nota > 5) {
            $this->errores['nota'] = 'La nota debe estar entre 1 y 5.';
        }

        $app = Aplicacion::getInstance();
        if (!$app->usuarioLogueado()) {
            $this->errores[] = 'Debes iniciar sesión para valorar.';
            return;
        }

        $usuario = $app->nombreUsuario();

        if (count($this->errores) === 0) {
            if (Valoracion::insertarValoracion($this->id_evento, $usuario, $nota, $comentario)) {
                // Redirección después de la inserción
                $redirectUrl = 'vistaEvento.php?id=' . $this->id_evento;
                header("Location: $redirectUrl");
                exit();
            } 
            else {
                $this->errores[] = 'Error al enviar valoracion.';
            }
        }
    }
}
?>
