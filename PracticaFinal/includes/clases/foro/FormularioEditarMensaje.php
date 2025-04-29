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
            $mensaje = MensajeForo::getMensajePorId($this->idMensaje);

            if (!$mensaje) {
                return '<p class="error">El mensaje no existe o no tienes permiso para editarlo.</p>';
            }

            $titulo = $mensaje->titulo;
            $contenido = $mensaje->mensaje;
            $autor = $mensaje->autor;
            $id_evento = $mensaje->evento;
            $eventoObj = $id_evento ? Evento::buscaPorId($id_evento) : null;
            $nombreEvento = $eventoObj ? $eventoObj->nombre : 'General';

            return <<<EOS
                <label for="titulo">Título:</label>
                <input type="text" name="titulo" id="titulo" value="{$titulo}" required><br><br>

                <p>Autor: <strong>{$autor}</strong></p>
                
                <p>Evento: <strong>{$nombreEvento}</strong></p>

                <label for="mensaje">Mensaje:</label>
                <textarea name="mensaje" id="mensaje">{$contenido}</textarea>

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

            if (empty($mensaje)) {
                $this->errores[] = "El mensaje no pueden estar vacíos.";
                return;
            }

            $mensajeOriginal = MensajeForo::getMensajePorId($this->idMensaje);
            $id_evento = $mensajeOriginal ? $mensajeOriginal->evento : null;

            if (!MensajeForo::editarMensaje($this->idMensaje, $titulo, $mensaje, $id_evento, $usuarioActual)) {
                $this->errores[] = "No se pudo actualizar el mensaje.";
            }

            // Redirigir al foro del evento o al foro general
            $urlRedireccion = $id_evento ? "foro.php?id={$id_evento}" : "foro.php";
            header("Location: $urlRedireccion");
            exit(); 
        }
    }
?>