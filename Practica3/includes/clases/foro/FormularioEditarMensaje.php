<?php
    namespace es\ucm\fdi\aw\foro;

    use es\ucm\fdi\aw\eventos\Evento;
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

            $titulo = $mensaje->titulo;
            $contenido = $mensaje->mensaje;
            $autor = $mensaje->autor;
            $id_evento = $mensaje->evento;
            $nombreEvento = $id_evento ? Evento::buscaPorId($id_evento) : 'General';

            return <<<EOS
                <form id="editar-mensaje" action="editar_mensaje.php?id={$this->idMensaje}" method="post">
                    <label for="titulo">Título:</label>
                    <input type="text" name="titulo" id="titulo" value="{$titulo}" required><br><br>

                    <p>Autor: <strong>{$autor}</strong></p>
                    
                    <p>Evento: <strong>{$nombreEvento}</strong></p>

                    <label for="mensaje">Mensaje:</label>
                    <textarea name="mensaje" id="mensaje" rows="4" required>{$contenido}</textarea>

                    <input type="hidden" name="idMensaje" value="{$this->idMensaje}">

                    <input type="submit" value="Guardar Cambios">
                    <a href="foro.php" style="margin-left: 10px;">Cancelar</a>
                </form>
            EOS;
        }

        protected function procesaFormulario(&$datos) {
            $app = Aplicacion::getInstance();
            $usuarioActual = $app->nombreUsuario();

            $titulo = trim($datos['titulo'] ?? '');
            $mensaje = trim($datos['mensaje'] ?? '');
            $evento = trim($datos['evento'] ?? null);

            if (empty($mensaje)) {
                $this->errores[] = "El mensaje no pueden estar vacíos.";
                return;
            }

            if (!mensajeForo::editarMensaje($this->idMensaje, $titulo, $mensaje, $evento, $usuarioActual)) {
                $this->errores[] = "No se pudo actualizar el mensaje.";
            }
        }
    }
?>