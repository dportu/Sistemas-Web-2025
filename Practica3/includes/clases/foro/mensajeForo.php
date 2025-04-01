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

        private $conn;

        private function __construct($id, $titulo, $autor, $mensaje, $evento, $fecha_publicacion) {
            $this->id = $id;
            $this->titulo = $titulo;
            $this->autor = $autor;
            $this->mensaje = $mensaje;
            $this->evento = $evento;
            $this->fecha_publicacion = $fecha_publicacion;

            $this->conn = Aplicacion::getInstance()->getConexionBd();
        }

        // Obtener lista de mensajes del foro (general o por evento)
        public static function getMensajes($id) {
            $conexion = Aplicacion::getInstance()->getConexionBd();
            $query = "SELECT f.*, e.nombre AS nombre_evento FROM foro f 
                      LEFT JOIN eventos e ON f.evento = e.id ";
        
            if ($id !== null) {
                $id = $conexion->real_escape_string($id);
                $query .= "WHERE f.evento = '$id' ";
            }
            else {
                $query .= "WHERE f.evento IS NULL "; // Solo mensajes generales
            }
        
            $query .= "ORDER BY fecha_publicacion DESC";
        
            $result = $conexion->query($query);
            $mensajes = [];
        
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $mensajes[] = new mensajeForo(
                        $row['id'],
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
        
        // Insertar un mensaje nuevo
        public static function agregarMensaje($titulo, $mensaje, $autor, $evento) {
            $conexion = Aplicacion::getInstance()->getConexionBd();
            $sql = "INSERT INTO foro (titulo, autor, mensaje, evento, fecha_publicacion) VALUES (?, ?, ?, ?, NOW())";
            
            $stmt = $conexion->prepare($sql);
            if (!$stmt) {
                die("Error en la preparación de la consulta: " . $conexion->error);
            }
        
            if (empty($evento)) {
                $evento = null;
            }
        
            $stmt->bind_param("sssi", $titulo, $autor, $mensaje, $evento);
            if (!$stmt->execute()) {
                die("Error al insertar el mensaje: " . $stmt->error);
            }
        
            // Obtener el ID del mensaje recién insertado
            $idMensaje = $conexion->insert_id;
        
            // Cerrar la consulta
            $stmt->close();
        
            // Crear y devolver el objeto mensajeForo
            return new mensajeForo($idMensaje, $titulo, $autor, $mensaje, $evento, date("Y-m-d H:i:s"));
        }

        // Editar un mensaje existente
        public static function editarMensaje($id_mensaje, $titulo, $mensaje, $evento, $autor) {
            $conexion = Aplicacion::getInstance()->getConexionBd();

            $titulo = $conexion->real_escape_string($titulo);
            $mensaje = $conexion->real_escape_string($mensaje);
            $autor = $conexion->real_escape_string($autor);
            $evento = $conexion->real_escape_string($evento);

            $sql = "UPDATE foro SET titulo = ?, mensaje = ?, evento = ? WHERE id = ? AND autor = ?";
            
            $stmt = $conexion->prepare($sql);
            if (!$stmt) {
                die("Error en la preparación de la consulta: " . $conexion->error);
            }

            if (empty($evento)) {
                $evento = null;
            }

            $stmt->bind_param("ssiis", $titulo, $mensaje, $evento, $id_mensaje, $autor);
            if (!$stmt->execute()) {
                die("Error al editar el mensaje: " . $stmt->error);
            }

            $stmt->close();

            $resultado = $conexion->query($sql);

            // Si la consulta afectó alguna fila, es porque se editó correctamente
            if ($resultado && $conexion->affected_rows > 0) {
                return true;
            } 
            else {
                return false;
            }
        }

        // Eliminar un mensaje (solo si el usuario es el autor)
        public static function eliminarMensaje($id_mensaje) {
            $conexion = Aplicacion::getInstance()->getConexionBd();
            $sql = "DELETE FROM foro WHERE id = ?";
            
            $stmt = $conexion->prepare($sql);
            if (!$stmt) {
                die("Error en la preparación de la consulta: " . $conexion->error);
            }

            $stmt->bind_param("i", $id_mensaje);
            if (!$stmt->execute()) {
                die("Error al eliminar el mensaje: " . $stmt->error);
            }

            $stmt->close();

            return true; // Se eliminó correctamente
        }


        public function getConexion() {
            return $this->conn;
        }

        public function getId() {
            return $this->id;
        }
        
        public function getTitulo() {
            return $this->titulo;
        }
        
        public function getAutor() {
            return $this->autor;
        }
        
        public function getMensaje() {
            return $this->mensaje;
        }
        
        public function getEvento() {
            return $this->evento;
        }
        
        public function getFechaPublicacion() {
            return $this->fecha_publicacion;
        }
        
    }