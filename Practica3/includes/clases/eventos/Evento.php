<?php
namespace es\ucm\fdi\aw\productos;

use es\ucm\fdi\aw\MagicProperties;
use es\ucm\fdi\aw\Aplicacion; //import de aplicacion?

class Evento {
    use MagicProperties;

    private $conn;

    private $id;
    private $nombreEvento;
    private $precio;
    private $descripcion;
    private $fecha;
    private $ubicacion;
    private $organizador;
    private $imagen;

    /* Gestion:
    - Mostrar evento no seria de evento, sino de las vistas
    - Mostrar lista eventos tampoco seria de evento
    */

    //pensado para hacer uso del autoincrement de id de la base de datos
    private function __construct($nombreEvento, $precio, $descripcion, $fecha, $ubicacion, $organizador, $imagen) {
        $this->nombreEvento = $nombreEvento;
        $this->precio = $precio;
        $this->descripcion = $descripcion;
        $this->fecha = $fecha;
        $this->ubicacion = $ubicacion;
        $this->organizador = $organizador;

        $this->conn = Aplicacion::getInstance()->getConexionBd();
        
        //la creacion como tal del evento se haria en alta evento
        //$this->id = $this->insertarEvento($conn);
    }

    //solo puede ser usada en altaevento
    private function insertarEvento() {
        $sql = "INSERT INTO eventos (nombreEvento, precio, descripcion, fecha, ubicacion, organizador, imagen) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        //preparamos la insercion
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $this->conn->error);
        }

        //vinculamos los parametros
        $stmt->bind_param("sdsssss", //sdsssss?
            $this->nombreEvento, 
            $this->precio, 
            $this->descripcion, 
            $this->fecha, 
            $this->ubicacion, 
            $this->organizador, 
            $this->imagen
        );

        if (!$stmt->execute()) {
            die("Error al insertar evento: " . $stmt->error);
        }
        
        $ret = $stmt->insert_id;

        $stmt->close(); //hay que cerrar ?

        return $ret;
    }

    public function compra($evento, $usuario, $precio, $cantidad) { //parametros de entrada provisionales
        $id = Evento::buscaPorNombre($evento); //se podria simplificar a busqueda solo por nombre
        if (Evento::buscaPorId($id)) { //comprobar tambien que queden entradas y reducirlas?
            //$usuario->addPuntos($precio / 4); se añadiria los puntos desde usuario?
            $ret = true;
            $mensaje = "¡Operación realizada con éxito!";
        }
        else {
            $ret = false;
            $mensaje = "Error en la compra";
        }

        //Mostramos un mensaje de éxito o de error
        echo "<script>alert('$mensaje');</script>";
        return $ret; //devolvemos si se ha realizado la compra o no
    }
    
    public static function altaEvento($nombreEvento, $precio, $descripcion, $fecha, $ubicacion, $organizador, $imagen) {
        $evento = Evento::buscaPorNombre($nombreEvento);
        if($evento != null) {
            $mensaje = "Ya existe un evento con ese nombre";
            $ret = false;
        }
        else {
            new Evento($nombreEvento, $precio, $descripcion, $fecha, $ubicacion, $organizador, $imagen);
            $mensaje = "Evento dado de alta con éxito";
            $ret = true;
        }

        //Mostramos un mensaje de éxito o de error
        echo "<script>alert('$mensaje');</script>";
        return $ret; //devolvemos si se ha realizado el alta o no
    }

    //modificacion sobre buscaUsuario
    public static function buscaPorNombre($nombreEvento) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM eventos WHERE nombreEvento='%s'", $conn->real_escape_string($nombreEvento));
        $rs = $conn->query($query);
        $result = false;
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $result = $fila['id'];
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    //modificacion sobre buscaPorId
    public static function buscaPorId($idEvento) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM eventos WHERE id=%d", $idEvento);
        $rs = $conn->query($query);
        $result = false;
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $result = new Evento($fila['nombreEvento'], $fila['precio'], $fila['descripcion'], $fila['fecha'], $fila['ubicacion'], $fila['organizador'], $fila['imagen']);
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    public function editarEvento($nombreEvento, $precio, $descripcion, $fecha, $ubicacion, $organizador, $imagen) {
        $this->nombreEvento = $nombreEvento;
        $this->precio = $precio;
        $this->descripcion = $descripcion;
        $this->fecha = $fecha;
        $this->ubicacion = $ubicacion;
        $this->organizador = $organizador;
        $this->imagen = $imagen;

        $sql = "UPDATE eventos SET nombreEvento=?, precio=?, descripcion=?, fecha=?, ubicacion=?, organizador=?, imagen=? WHERE id=?";

        //preparamos la insercion
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $this->conn->error);
        }

        //vinculamos los parametros
        $stmt->bind_param("sdsssssi",
            $this->nombreEvento, 
            $this->precio, 
            $this->descripcion, 
            $this->fecha, 
            $this->ubicacion, 
            $this->organizador, 
            $this->imagen,
            $this->id
        );

        if (!$stmt->execute()) {
            die("Error al modificar evento: " . $stmt->error);
        }
        
        $stmt->close(); //hay que cerrar ?
    }

    public function eliminarEvento() {
        $sql = "DELETE FROM eventos WHERE id=?";

        //preparamos la insercion
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $this->conn->error);
        }

        //vinculamos los parametros
        $stmt->bind_param("i", $this->id);

        if (!$stmt->execute()) {
            die("Error al eliminar evento: " . $stmt->error);
        }
        
        $stmt->close(); //hay que cerrar ?
    }

}

    /*

    public static function buscaPorId($idUsuario)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM Usuarios WHERE id=%d", $idUsuario);
        $rs = $conn->query($query);
        $result = false;
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $result = new Usuario($fila['nombreUsuario'], $fila['password'], $fila['nombre'], $fila['id']);
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }
    
    private static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    private static function cargaRoles($usuario)
    {
        $roles=[];
            
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT RU.rol FROM RolesUsuario RU WHERE RU.usuario=%d"
            , $usuario->id
        );
        $rs = $conn->query($query);
        if ($rs) {
            $roles = $rs->fetch_all(MYSQLI_ASSOC);
            $rs->free();

            $usuario->roles = [];
            foreach($roles as $rol) {
                $usuario->roles[] = $rol['rol'];
            }
            return $usuario;

        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return false;
    }
   
    private static function inserta($usuario)
    {
        $result = false;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("INSERT INTO Usuarios(nombreUsuario, nombre, password) VALUES ('%s', '%s', '%s')"
            , $conn->real_escape_string($usuario->nombreUsuario)
            , $conn->real_escape_string($usuario->nombre)
            , $conn->real_escape_string($usuario->password)
        );
        if ( $conn->query($query) ) {
            $usuario->id = $conn->insert_id;
            $result = self::insertaRoles($usuario);
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }
   
    private static function insertaRoles($usuario)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        foreach($usuario->roles as $rol) {
            $query = sprintf("INSERT INTO RolesUsuario(usuario, rol) VALUES (%d, %d)"
                , $usuario->id
                , $rol
            );
            if ( ! $conn->query($query) ) {
                error_log("Error BD ({$conn->errno}): {$conn->error}");
                return false;
            }
        }
        return $usuario;
    }
    
    private static function actualiza($usuario)
    {
        $result = false;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("UPDATE Usuarios U SET nombreUsuario = '%s', nombre='%s', password='%s' WHERE U.id=%d"
            , $conn->real_escape_string($usuario->nombreUsuario)
            , $conn->real_escape_string($usuario->nombre)
            , $conn->real_escape_string($usuario->password)
            , $usuario->id
        );
        if ( $conn->query($query) ) {
            $result = self::borraRoles($usuario);
            if ($result) {
                $result = self::insertaRoles($usuario);
            }
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        
        return $result;
    }
   
    private static function borraRoles($usuario)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("DELETE FROM RolesUsuario RU WHERE RU.usuario = %d"
            , $usuario->id
        );
        if ( ! $conn->query($query) ) {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
            return false;
        }
        return $usuario;
    }
    
    private static function borra($usuario)
    {
        return self::borraPorId($usuario->id);
    }
    
    private static function borraPorId($idUsuario)
    {
        if (!$idUsuario) {
            return false;
        } 
        /* Los roles se borran en cascada por la FK
         * $result = self::borraRoles($usuario) !== false;
         
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("DELETE FROM Usuarios U WHERE U.id = %d"
            , $idUsuario
        );
        if ( ! $conn->query($query) ) {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
            return false;
        }
        return true;
    }

    private $id;

    private $nombreUsuario;

    private $password;

    private $nombre;

    private $roles;

    private function __construct($nombreUsuario, $password, $nombre, $id = null, $roles = [])
    {
        $this->id = $id;
        $this->nombreUsuario = $nombreUsuario;
        $this->password = $password;
        $this->nombre = $nombre;
        $this->roles = $roles;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNombreUsuario()
    {
        return $this->nombreUsuario;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function añadeRol($role)
    {
        $this->roles[] = $role;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function tieneRol($role)
    {
        if ($this->roles == null) {
            self::cargaRoles($this);
        }
        return array_search($role, $this->roles) !== false;
    }

    public function compruebaPassword($password)
    {
        return password_verify($password, $this->password);
    }

    public function cambiaPassword($nuevoPassword)
    {
        $this->password = self::hashPassword($nuevoPassword);
    }
    
    public function guarda()
    {
        if ($this->id !== null) {
            return self::actualiza($this);
        }
        return self::inserta($this);
    }
    
    public function borrate()
    {
        if ($this->id !== null) {
            return self::borra($this);
        }
        return false;
    }
*/
