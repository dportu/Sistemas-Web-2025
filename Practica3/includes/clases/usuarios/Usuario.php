<?php
namespace es\ucm\fdi\aw\usuarios;

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\MagicProperties;

class Usuario
{
    use MagicProperties;

    public const ADMIN_ROLE = 'administrador';
    public const PROMOTOR_ROLE = 'promotor';
    public const CLIENTE_ROLE = 'cliente';


    public static function login($username, $password) {
        $usuario = self::buscaUsuario($username);
        if ($usuario && $usuario->compruebaPassword($password)) {
            return $usuario;
        }
        return false;
    }

    public static function crea($username, $password, $email, $rol = self::CLIENTE_ROLE, $puntos = 0) {
        // Hashear la contraseña antes de almacenarla
        $hashedPassword = self::hashPassword($password);
        $usuario = new Usuario($username, $hashedPassword, $email, $rol, $puntos);
        if ($usuario->guarda()) {
            return $usuario;
        } else {
            error_log("Error al guardar el usuario: $username");
            return false;
        }
    }

    public static function buscaUsuario($username) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf(
            "SELECT * FROM usuarios WHERE username='%s'",
            $conn->real_escape_string($username)
        );
        $rs = $conn->query($query);
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                return new Usuario(
                    $fila['username'],
                    $fila['password'],
                    $fila['email'],
                    $fila['rol'],
                    $fila['puntos']
                );
            }
            $rs->free();
        }
        error_log("Error BD ({$conn->errno}): {$conn->error}");
        return false;
    }

    private static function cargaRoles($usuario) {
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
   
    private static function inserta($usuario) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf(
            "INSERT INTO usuarios (username, email, password, rol, puntos) 
            VALUES ('%s', '%s', '%s', '%s', %d)",
            $conn->real_escape_string($usuario->username),
            $conn->real_escape_string($usuario->email),
            $conn->real_escape_string($usuario->password),
            $conn->real_escape_string($usuario->rol),
            $usuario->puntos
        );
        return $conn->query($query);
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
    
    private static function actualiza($usuario) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf(
            "UPDATE usuarios SET 
                email = '%s', 
                password = '%s', 
                rol = '%s', 
                puntos = %d 
            WHERE username = '%s'",
            $conn->real_escape_string($usuario->email),
            $conn->real_escape_string($usuario->password),
            $conn->real_escape_string($usuario->rol),
            $usuario->puntos,
            $conn->real_escape_string($usuario->username)
        );
        return $conn->query($query);
    }

   
    private static function borraRoles($usuario) {
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
    
    private static function borra($usuario) {
        return self::borraPorUsername($usuario->username);
    }
    
    private static function borraPorUsername($username) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf(
            "DELETE FROM usuarios WHERE username = '%s'",
            $conn->real_escape_string($username)
        );
        return $conn->query($query);
    }

    private $username;
    private $password;
    private $email;
    private $rol;
    private $puntos;

    private function __construct($username, $password, $email, $rol, $puntos) {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->rol = $rol;
        $this->puntos = $puntos;
    }

    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getRol() { return $this->rol; }
    public function getPuntos() { return $this->puntos; }


   

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

    private static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    public function guarda() {
        // Si el usuario ya existe, actualiza; de lo contrario, inserta
        if ($this->username !== null) {
            return self::actualiza($this);
        } else {
            return self::inserta($this);
        }
    }
    
    
    public function borrate()
    {
        return self::borra($this);
    }

} ?>
