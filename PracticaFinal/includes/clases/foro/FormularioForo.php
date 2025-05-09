<?php

    namespace es\ucm\fdi\aw\foro;

    use es\ucm\fdi\aw\Aplicacion;
    use es\ucm\fdi\aw\Formulario;
    use es\ucm\fdi\aw\eventos\Evento;

    class FormularioForo extends Formulario {

        private $idEvento;

        public function __construct($id_evento = null, $parent_id = null) {
            parent::__construct(
                'formForo', 
                ['urlRedireccion' => $id_evento ? 'foro.php?id='.$id_evento : 'foro.php']
            );
            $this->parent_id = $parent_id;
        }

        protected function generaCamposFormulario(&$datos) {
            // Recuperamos los valores de los datos del formulario.
            $html = '';
            $titulo = $datos['titulo'] ?? '';
            $mensaje = $datos['mensaje'] ?? '';
            if ($this->idEvento) {
                $evento = Evento::buscaPorId($this->idEvento)->nombre ?? 'General';
            } else {
                $evento = 'General';
            }
            $autor = Aplicacion::getInstance()->nombreUsuario();

            
            $erroresCampos = self::generaErroresCampos(['titulo', 'mensaje'], $this->errores, 'span', ['class' => 'error']);
            $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
            if ($this->parent_id) {
                $html .= '<input type="hidden" name="parent_id" value="'.$this->parent_id.'">';
            }

           
            if (Aplicacion::getInstance()->usuarioLogueado()){
                $html = <<<EOF
                $htmlErroresGlobales
                        <h3>Escribe tu mensaje</h3>

                        <label for="titulo">Título:</label>
                        <input id="titulo" type="text" name="titulo" value="$titulo" required ><br>
                        {$erroresCampos['titulo']}

                        <!-- Autor y Evento, ocultos -->
                        <input type="hidden" name="autor" value="$autor">
                        <input type="hidden" name="evento" value="$evento">

                        <p>Publicando como: <strong> $autor </strong></p>
                        <p>Evento: <strong> $evento </strong></p>

                        <label for="mensaje">Mensaje:</label>
                        <textarea id="mensaje" name="mensaje" rows="4" required>$mensaje</textarea>
                        {$erroresCampos['mensaje']}
                            
                        <input type="hidden" name="evento" value="{$this->idEvento}">
                        
                        <button type="submit" name="enviar">Publicar mensaje</button>
                EOF;
            }
            else {
                $html = <<<EOF
                    $htmlErroresGlobales
                        <p>Para publicar un mensaje, debes <a href='login.php'> inicia sesión</a>.</p>
                    EOF;
            }

            $html .= '<input type="hidden" name="parent_id" value="'.$this->parent_id.'">';
            return $html;
        }

        protected function procesaFormulario(&$datos) {
            $this->errores = [];


            $titulo = trim($datos['titulo'] ?? '');
            $mensaje = trim($datos['mensaje'] ?? '');
            $evento = isset($datos['evento']) && !empty($datos['evento']) ? trim($datos['evento']) : null;
            $parent_id = $datos['parent_id'] ?? null;

            if (empty($titulo)) {
                $this->errores['titulo'] = 'El título no puede estar vacío.';
            }

            if (empty($mensaje)) {
                $this->errores['mensaje'] = 'El mensaje no puede estar vacío.';
            }


            $app = Aplicacion::getInstance();
            if (!$app->usuarioLogueado()) {
                $this->errores[] = 'Debes iniciar sesión para publicar.';
                return;
            }

           
            $usuario = $app->nombreUsuario();

            if (count($this->errores) === 0) {
                 //Ahora pasamos tambien el parent_id
                if (MensajeForo::agregarMensaje($titulo, $mensaje, $usuario, $evento, $parent_id)) {
                    
                    $redirectUrl = 'foro.php' . ($evento ? "?id=$evento" : '');
                    header("Location: $redirectUrl");
                    exit();
                } 
                else {
                    $this->errores[] = 'Error al enviar el mensaje.';
                }
            }
        }
    }

?>
