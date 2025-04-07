<?php
namespace es\ucm\fdi\aw\usuarios;

use es\ucm\fdi\aw\Aplicacion;

class Usuario
{
    public const ADMIN_ROLE = 'administrador';
    public const PROMOTOR_ROLE = 'promotor';
    public const CLIENTE_ROLE = 'cliente';

    //  ATRIBUTOS
    private $id;
    private $username;
    private $password;
    private $email;
    private $rol;
    private $puntos;

    //  CONSTRUCTOR
    private function __construct($username, $password, $email, $rol, $puntos) //pq no se consigue el id en el constructor?
    {
        //$this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->rol = $rol;
        $this->puntos = $puntos;
    }

    
    public static function login($username, $password)
    {
        $usuario = self::buscaUsuario($username);
        if ($usuario && $usuario->compruebaPassword($password)) {
            return $usuario;
        }
        return false;
    }

    public static function crea($username, $password, $email, $rol, $puntos)
    {
        $user = new Usuario($username, self::hashPassword($password), $email, $rol, $puntos);
        return $user->guarda();
    }


    //  BUSQUEDAS

    public static function buscaUsuario($username)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM usuarios WHERE username='%s'", $conn->real_escape_string($username));
        $rs = $conn->query($query);
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $user = new Usuario($fila['username'], $fila['password'], $fila['email'], $fila['rol'], $fila['puntos']);
                $rs->free();
                return $user;
            }
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return false;
    }

    public static function buscaPorId($idUsuario)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM usuarios WHERE id=%d", $conn->real_escape_string($idUsuario));
        $rs = $conn->query($query);
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $user = new Usuario($fila['username'], $fila['password'], $fila['email'], $fila['rol'], $fila['puntos']);
                $rs->free();
                return $user;
            }
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return false;
    }

    private static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    private static function inserta($usuario)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf(
            "INSERT INTO usuarios (username, password, email, rol, puntos) VALUES ('%s', '%s', '%s', '%s', '%d')",
            $conn->real_escape_string($usuario->username),
            $conn->real_escape_string($usuario->password),
            $conn->real_escape_string($usuario->email),
            $conn->real_escape_string($usuario->rol),
            $usuario->puntos
        );
        if ($conn->query($query)) {
            $usuario->id = $conn->insert_id;
            return $usuario;
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
            return false;
        }
    }

    public static function actualiza($usuario) {
        $conn = Aplicacion::getInstance()->getConexionBd();
    
        $query = "UPDATE usuarios SET email=?, password=?, rol=?, puntos=? WHERE username=?";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            return ["error" => "Error en la preparación de la consulta: " . $conn->error];
        }
        $stmt->bind_param(
            "sssis",
            $usuario->email, 
            $usuario->password,
            $usuario->rol, 
            $usuario->puntos, 
            $usuario->username
        );
    
        if ($stmt->execute()) {
            return ["success" => true];
        } else {
            return ["error" => "Error al actualizar: " . $stmt->error];
        }
    }
    

    //  BORRADOS

    private static function borra($usuario)
    {
        return self::borraPorId($usuario->id);
    }

    private static function borraPorId($idUsuario)
    {
        if (!$idUsuario) {
            return false;
        }
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("DELETE FROM usuarios WHERE id = %d", $conn->real_escape_string($idUsuario));
        if ($conn->query($query)) {
            return true;
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
            return false;
        }
    }

    public function addPuntos($p) {
        $this->puntos = $this->puntos + $p;
        return $this->puntos;
    }

    

    //  GETTERS
  

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getRol()
    {
        return $this->rol;
    }

    public function getPuntos() {
        return $this->puntos;
    }



    public function tieneRol($rol)
    {
        return $this->rol === $rol;
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
}