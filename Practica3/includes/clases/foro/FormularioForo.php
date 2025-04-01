<?php

    namespace es\ucm\fdi\aw\foro;

    use es\ucm\fdi\aw\Aplicacion;
    use es\ucm\fdi\aw\Formulario;
    use es\ucm\fdi\aw\eventos\Evento;

    class FormularioForo extends Formulario {

        private $idEvento;  // Guardamos el ID del evento, si existe.

        public function __construct($idEvento) {
            $this->idEvento = $idEvento;
            parent::__construct('formForo');
        }

        protected function generaCamposFormulario(&$datos) {
            // Recuperamos los valores de los datos del formulario.
            $titulo = $datos['titulo'] ?? '';
            $mensaje = $datos['mensaje'] ?? '';
            if ($this->idEvento) {
                $evento = Evento::buscaPorId($this->idEvento)->nombre ?? 'General';
            } else {
                $evento = 'General';
            }
            $autor = Aplicacion::getInstance()->nombreUsuario();
            $esAnonimo = isset($datos['anonimo']) && $datos['anonimo'] === 'on';

            // Generamos los errores de campos si existen.
            $erroresCampos = self::generaErroresCampos(['titulo', 'mensaje'], $this->errores, 'span', ['class' => 'error']);
            $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);

            // Generamos el HTML del formulario.
            if (Aplicacion::getInstance()->usuarioLogueado()){
                $html = <<<EOF
                $htmlErroresGlobales
                    <form id="nuevo-mensaje" action="procesar_foro.php" method="post">
                        
                        <h3>Escribe tu mensaje</h3>

                        <label for="titulo">Título:</label>
                        <input id="titulo" type="text" name="titulo" value="$titulo" required /><br>
                        {$erroresCampos['titulo']}

                        <!-- Autor y Evento, ocultos o editables según si es anónimo -->
                        <input type="hidden" name="autor" value="$autor">
                        <input type="hidden" name="evento" value="$evento">

                        <p>Publicando como: <strong> $autor </strong></p>
                        <p>Evento: <strong> $evento </strong></p>

                        <!-- TODO: Opción de enviar como anónimo -->
                        <label for="anonimo">Publicar como anónimo:</label>
                        <input type="checkbox" name="anonimo" id="anonimo" $esAnonimo>
                        <br>

                        <label for="mensaje">Mensaje:</label>
                        <textarea id="mensaje" name="mensaje" rows="4" required>$mensaje</textarea>
                        {$erroresCampos['mensaje']}
                            
                        <input type="hidden" name="evento" value="{$this->idEvento}">
                        
                        <button type="submit" name="enviar">Publicar mensaje</button>
                            
                    </form>
                EOF;
            }
            else {
                $html = <<<EOF
                    $htmlErroresGlobales
                        <p>Para publicar un mensaje, debes <a href='login.php'> inicia sesión</a>.</p>
                    EOF;
            }

            return $html;
        }

        protected function procesaFormulario(&$datos) {
            $this->errores = [];

            // Validación de título y mensaje.
            $titulo = trim($datos['titulo'] ?? '');
            $mensaje = trim($datos['mensaje'] ?? '');
            $evento = isset($datos['evento']) && !empty($datos['evento']) ? trim($datos['evento']) : null;

            if (empty($titulo)) {
                $this->errores['titulo'] = 'El título no puede estar vacío.';
            }

            if (empty($mensaje)) {
                $this->errores['mensaje'] = 'El mensaje no puede estar vacío.';
            }

            // Verificar si el usuario está logueado
            $app = Aplicacion::getInstance();
            if (!$app->usuarioLogueado()) {
                $this->errores[] = 'Debes iniciar sesión para publicar.';
                return;
            }

            // Obtener los datos del usuario
            $usuario = $app->nombreUsuario();

            // Si no hay errores, se inserta el mensaje en la base de datos
            if (count($this->errores) === 0) {
                // Intentar agregar el mensaje
                if (MensajeForo::agregarMensaje($titulo, $mensaje, $usuario, $evento)) {
                    // Redirección después de la inserción
                    $redirectUrl = 'foro.php' . ($evento ? "?id=$evento" : '');
                    echo "<script>alert('Mensaje enviado con éxito'); window.location = '$redirectUrl';</script>";
                    exit();
                } 
                else {
                    $this->errores[] = 'Error al enviar el mensaje.';
                }
            }
        }
    }

?>
