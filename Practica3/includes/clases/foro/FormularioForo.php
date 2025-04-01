<?php

    namespace es\ucm\fdi\aw\foro;

    use es\ucm\fdi\aw\Aplicacion;
    use es\ucm\fdi\aw\Formulario;
    use es\ucm\fdi\aw\eventos\Evento;

    class FormularioForo extends Formulario {

        private $idEvento;  // Guardamos el ID del evento, si existe.

        public function __construct($idEvento = null) {
            // Llamamos al constructor de la clase base.
            $this->idEvento = $idEvento;
            parent::__construct('formForo');
        }

        protected function generaCamposFormulario(&$datos) {
            // Recuperamos los valores de los datos del formulario.
            $titulo = $datos['titulo'] ?? '';
            $mensaje = $datos['mensaje'] ?? '';
            if ($this->idEvento) {
                $evento = Evento::buscaPorId($this->idEvento)->getNombre() ?? 'General';
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
            // TODO: usar clase mensajeForo
            $this->errores = [];
    
            // Validación de título y mensaje.
            $titulo = trim($datos['titulo'] ?? '');
            $mensaje = trim($datos['mensaje'] ?? '');
    
            if (empty($titulo)) {
                $this->errores['titulo'] = 'El título no puede estar vacío.';
            }
    
            if (empty($mensaje)) {
                $this->errores['mensaje'] = 'El mensaje no puede estar vacío.';
            }
    
            // Si el usuario no está logueado y no quiere publicar de forma anónima, se muestra un error.
            if (Aplicacion::getInstance()->usuarioLogueado() && isset($datos['anonimo'])) {
                $this->errores[] = 'Debes estar registrado.';
            }
    
            // Si no hay errores, guardamos el mensaje en la base de datos.
            if (count($this->errores) === 0) {
                $evento = $this->idEvento;
                $usuario = Aplicacion::getInstance()->nombreUsuario();
    
                // Insertamos el mensaje en la base de datos.
                $sql = "INSERT INTO foro (titulo, autor, mensaje, evento) VALUES (?, ?, ?, ?)";
                $stmt = Aplicacion::getInstance()->getConexionBd()->prepare($sql);
    
                // Usamos un tipo de parámetro adecuado para cada valor.
                $stmt->bind_param("ssssi", $titulo, $usuario, $mensaje, $evento);
                if (!$stmt->execute()) {
                    $this->errores[] = 'Error al enviar el mensaje: ' . $stmt->error;
                }
            }
        }
    }

?>
