<?php

class Usuario
{
    public const ADMIN_ROLE = 'administrador';
    public const PROMOTOR_ROLE = 'promotor';
    public const CLIENTE_ROLE = 'cliente';

    public static function login($username, $password)
    {
        $usuario = self::buscaUsuario($username);
        if ($usuario && $usuario->compruebaPassword($password)) {
            return $usuario;
        }
        return false;
    }

    public static function crea($username, $password, $email, $rol)
    {
        $user = new Usuario($username, self::hashPassword($password), $email, $rol);
        return $user->guarda();
    }

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
        $query = sprintf("SELECT * FROM usuarios WHERE id=%d", $idUsuario);
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
            "INSERT INTO usuarios (username, password, email, rol, puntos) VALUES ('%s', '%s', '%s', '%s', '%s')",
            $conn->real_escape_string($usuario->username),
            $conn->real_escape_string($usuario->password),
            $conn->real_escape_string($usuario->email),
            $conn->real_escape_string($usuario->rol),
            $conn->real_escape_string($usuario->puntos)
        );
        if ($conn->query($query)) {
            $usuario->id = $conn->insert_id;
            return $usuario;
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
            return false;
        }
    }

    private static function actualiza($usuario)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf(
            "UPDATE usuarios SET username='%s', password='%s', email='%s', rol='%s', puntos='%s' WHERE id=%d",
            $conn->real_escape_string($usuario->username),
            $conn->real_escape_string($usuario->password),
            $conn->real_escape_string($usuario->email),
            $conn->real_escape_string($usuario->rol),
            $conn->real_escape_string($usuario->puntos),
            $usuario->id
        );
        if ($conn->query($query)) {
            return true;
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
            return false;
        }
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
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("DELETE FROM usuarios WHERE id = %d", $idUsuario);
        if ($conn->query($query)) {
            return true;
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
            return false;
        }
    }

    private $id;
    private $username;
    private $password;
    private $email;
    private $rol;
    private $puntos;

    private function __construct($username, $password, $email, $rol, $puntos = 0, $id = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->rol = $rol;
        $this->puntos = $puntos;
    }

    public function getId()
    {
        return $this->id;
    }

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

    public function getPuntos()
    {
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