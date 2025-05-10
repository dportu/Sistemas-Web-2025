<?php
    namespace es\ucm\fdi\aw\foro;

    use es\ucm\fdi\aw\MagicProperties;
    use es\ucm\fdi\aw\Aplicacion;

    class MensajeForo { //no hay un foro como tal, sino menajes sobre un evento en concreto
        use MagicProperties;

        private $id;
        private $titulo;
        private $autor;
        private $mensaje;
        private $evento;
        private $fecha_publicacion;
        private $parent_id;

        private $conn;

        private function __construct($id, $titulo, $autor, $mensaje, $evento, $fecha_publicacion, $parent_id) {
            $this->id = $id;
            $this->titulo = $titulo;
            $this->autor = $autor;
            $this->mensaje = $mensaje;
            $this->evento = $evento;
            $this->fecha_publicacion = $fecha_publicacion;
            $this->parent_id = $parent_id;

            $this->conn = Aplicacion::getInstance()->getConexionBd();
            
        }

        // Obtener lista de mensajes del foro (general o por evento)
        public static function getMensajes($id_evento = null, $parent_id = null) {
            $conexion = Aplicacion::getInstance()->getConexionBd();
            
            $query = "SELECT f.*, e.nombre AS nombre_evento 
                      FROM foro f 
                      LEFT JOIN eventos e ON f.evento = e.id 
                      WHERE 1=1"; // WHERE inicial
            
            $params = [];
            $types = '';
        
            // Condición para evento
            if ($id_evento !== null) {
                $query .= " AND f.evento = ?";
                $params[] = $id_evento;
                $types .= 'i';
            }
            
  
            $query .= " ORDER BY fecha_publicacion DESC";
            
            $stmt = $conexion->prepare($query);
            
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute(); // <-- Ejecutar el prepared statement
            $result = $stmt->get_result(); // <-- Obtener resultados
            $mensajes = [];
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $mensajes[] = new MensajeForo(
                        $row['id'],
                        $row['titulo'], 
                        $row['autor'], 
                        $row['mensaje'], 
                        $row['evento'], 
                        $row['fecha_publicacion'],
                        $row['parent_id']
                    );
                }
                $result->free();
            }
            
            $stmt->close();
            return $mensajes;
        }
        
        public static function getRespuestas($parent_id) {
            $conexion = Aplicacion::getInstance()->getConexionBd();
            
            $sql = "SELECT f.*, e.nombre AS nombre_evento 
                    FROM foro f
                    LEFT JOIN eventos e ON f.evento = e.id
                    WHERE f.parent_id = ?
                    ORDER BY fecha_publicacion ASC";  // Orden ascendente para ver respuestas más antiguas primero
                    
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $parent_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $respuestas = [];
            while ($row = $result->fetch_assoc()) {
                $respuestas[] = new MensajeForo(
                    $row['id'],
                    $row['titulo'],
                    $row['autor'],
                    $row['mensaje'],
                    $row['evento'],
                    $row['fecha_publicacion'],
                    $row['parent_id']
                );
            }
            
            return $respuestas;
        }
        
        // Insertar un mensaje nuevo
        public static function agregarMensaje($titulo, $mensaje, $autor, $evento, $parent_id = null) {
            $conexion = Aplicacion::getInstance()->getConexionBd();
            $sql = "INSERT INTO foro (titulo, autor, mensaje, evento, fecha_publicacion, parent_id) 
                    VALUES (?, ?, ?, ?, NOW(), ?)";
            $stmt = $conexion->prepare($sql);
            if (!$stmt) {
                die("Error en la preparación de la consulta: " . $conexion->error);
            }
        
            if (empty($evento)) {
                $evento = null;
            }
        
            $stmt->bind_param("sssii", $titulo, $autor, $mensaje, $evento , $parent_id);
            if (!$stmt->execute()) {
                die("Error al insertar el mensaje: " . $stmt->error);
            }
        
            $conexion->insert_id;
        
            $stmt->close();
        
            return true;
        }

        // Editar un mensaje existente
        public static function editarMensaje($id_mensaje, $titulo, $mensaje, $evento) {
            $conexion = Aplicacion::getInstance()->getConexionBd();
        
            $titulo = $conexion->real_escape_string($titulo);
            $mensaje = $conexion->real_escape_string($mensaje);
        
            if ($evento === 'General') { 
                $evento = null;
            }
        
            if ($evento === null) {
                $sql = "UPDATE foro SET titulo = ?, mensaje = ?, evento = NULL WHERE id = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("ssi", $titulo, $mensaje, $id_mensaje);
            } else {
                $evento = $conexion->real_escape_string($evento);
                $sql = "UPDATE foro SET titulo = ?, mensaje = ?, evento = ? WHERE id = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("ssis", $titulo, $mensaje, $evento, $id_mensaje);
            }
        
            if (!$stmt) {
                die("Error en la preparación de la consulta: " . $conexion->error);
            }
        
            if (!$stmt->execute()) {
                die("Error al editar el mensaje: " . $stmt->error);
            }
        
            $stmt->close();
        
            if ($conexion->affected_rows > 0) {
                // Redirigir al foro después de la edición
                header("Location: foro.php" . ($evento ? "?id=" . $evento : ""));
                exit();
            }
        
            return false;
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

            return true;
        }

        // Obtener un mensaje por su ID
        public static function getMensajePorId($id_mensaje) {
            $conexion = Aplicacion::getInstance()->getConexionBd();
            
            $stmt = $conexion->prepare("SELECT f.*, e.nombre AS nombre_evento FROM foro f LEFT JOIN eventos e ON f.evento = e.id WHERE f.id = ?");
            $stmt->bind_param("i", $id_mensaje);
            $stmt->execute();
            $resultado = $stmt->get_result();
        
            $result = null;
        
            if ($resultado->num_rows === 1) {
                $fila = $resultado->fetch_assoc();
                $result = new MensajeForo($id_mensaje, $fila['titulo'], $fila['autor'], $fila['mensaje'], $fila['evento'], $fila['fecha_publicacion'], $fila['parent_id']);
            } else {
                error_log("Error BD ({$conexion->errno}): {$conexion->error}");
            }
        
            $stmt->close();
            return $result;
        }

        public function getConexion() {
            return $this->conn;
        }

        public function getId() {
            return $this->id;
        }

        public function getParentId() {
            return $this->parent_id;
        }

        public function setParentId($parent_id) {
            $this->parent_id = $parent_id;
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