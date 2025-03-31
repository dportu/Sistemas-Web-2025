    <?php
    namespace es\ucm\fdi\aw\foro;

    use es\ucm\fdi\aw\MagicProperties;
    use es\ucm\fdi\aw\Aplicacion;

    class mensajeForo { //no hay un foro como tal, sino menajes sobre un evento en concreto
        use MagicProperties;

        private $id;
        private $titulo;
        private $autor;
        private $mensaje;
        private $evento;
        private $fecha_publicacion;

        private $conexion;

        /*
            Obtener mensajes
            Crear un mensaje
            Editar un mensaje
            Eliminar un mensaje
        */

        private function __construct($titulo, $autor, $mensaje, $evento, $fecha_publicacion) {
            $this->titulo = $titulo;
            $this->autor = $autor;
            $this->mensaje = $mensaje;
            $this->evento = $evento;
            $this->fecha_publicacion = $fecha_publicacion;

            $this->conexion = Aplicacion::getInstance()->getConexionBd();
        }

        public static function getMensajes($id) {
            $conexion = Aplicacion::getInstance()->getConexionBd();
            $query = "SELECT f.*, e.nombre AS nombre_evento FROM foro f 
                    LEFT JOIN eventos e ON f.evento = e.id";
            
            if ($id != null) {
                $query .= "WHERE f.evento = $id ";
            }

            $query .= "ORDER BY fecha_publicacion DESC";

            $result = $conexion->query($query);
            $mensajes = [];

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $mensajes[] = new mensajeForo(
                        $row['titulo'], 
                        $row['autor'], 
                        $row['mensaje'], 
                        $row['evento'], 
                        $row['fecha_publicacion']
                    );
                }
            }
            return $mensajes;
        }

        
    }