<?php
    namespace es\ucm\fdi\aw\foro;

    use es\ucm\fdi\aw\Formulario;
    use es\ucm\fdi\aw\Aplicacion;

    class FormularioEditarMensaje extends Formulario {

        private $idMensaje;

        public function __construct($idMensaje) {
            parent::__construct('formEditarMensaje', ['urlRedireccion' => "foro.php"]);
            $this->idMensaje = $idMensaje;
        }

        protected function generaCamposFormulario(&$datos) {
            $mensaje = mensajeForo::getMensajePorId($this->idMensaje);

            if (!$mensaje) {
                return '<p class="error">El mensaje no existe o no tienes permiso para editarlo.</p>';
            }

            $titulo = $mensaje->getTitulo();
            $contenido = $mensaje->getMensaje();
            $evento = $mensaje->getEvento() ?? '';

            return <<<EOS
                <label for="titulo">Título:</label>
                <input type="text" name="titulo" value="{$titulo}" required>

                <label for="mensaje">Mensaje:</label>
                <textarea name="mensaje" rows="4" required>{$contenido}</textarea>

                <input type="hidden" name="idMensaje" value="{$this->idMensaje}">

                <input type="submit" value="Guardar Cambios">
                <a href="foro.php">Cancelar</a>
            EOS;
        }

        protected function procesaFormulario(&$datos) {
            $app = Aplicacion::getInstance();
            $usuarioActual = $app->nombreUsuario();

            $titulo = trim($datos['titulo'] ?? '');
            $mensaje = trim($datos['mensaje'] ?? '');
            $evento = trim($datos['evento'] ?? null);

            if (empty($titulo) || empty($mensaje)) {
                $this->errores[] = "El título y el mensaje no pueden estar vacíos.";
                return;
            }

            if (!mensajeForo::editarMensaje($this->idMensaje, $titulo, $mensaje, $evento, $usuarioActual)) {
                $this->errores[] = "No se pudo actualizar el mensaje.";
            }
        }
    }
?>