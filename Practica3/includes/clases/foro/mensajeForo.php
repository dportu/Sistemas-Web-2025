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

        private function __construct($titulo, $autor, $mensaje, $evento, $fecha_publicacion) {
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
                $query .= "WHERE f.evento = $id ";
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
        public function agregarMensaje($titulo, $mensaje, $autor, $email, $evento) {
            $sql = "INSERT INTO foro (titulo, autor, email, mensaje, evento) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conexion->prepare($sql);
            if (empty($evento)) {
                $evento = null; // Si está vacío, asignamos NULL
            }
            $stmt->bind_param("ssssi", $titulo, $autor, $email, $mensaje, $evento);
            return $stmt->execute();
        }

        // Editar un mensaje existente
        public function editarMensaje($id_mensaje, $titulo, $mensaje, $evento, $autor) {
            $sql = "UPDATE foro SET titulo = ?, mensaje = ?, evento = ? WHERE id = ? AND autor = ?";
            $stmt = $this->conexion->prepare($sql);
            if (empty($evento)) {
                $evento = null;
            }
            $stmt->bind_param("ssiis", $titulo, $mensaje, $evento, $id_mensaje, $autor);
            return $stmt->execute();
        }

        // Eliminar un mensaje (solo si el usuario es el autor)
        public function eliminarMensaje($id_mensaje, $autor) {
            $stmt = $this->conexion->prepare("DELETE FROM foro WHERE id = ? AND autor = ?");
            $stmt->bind_param("is", $id_mensaje, $autor);
            return $stmt->execute();
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

    /*

        // Obtener un mensaje por su ID
        public function obtenerMensajePorId($id_mensaje) {
            $stmt = $this->conexion->prepare("SELECT * FROM foro WHERE id = ?");
            $stmt->bind_param("i", $id_mensaje);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }

    */