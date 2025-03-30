<?php
namespace es\ucm\fdi\aw\usuarios;

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\Formulario;

class FormularioRegistro extends Formulario
{
    public function __construct() {
        parent::__construct('formRegistro', [
            'urlRedireccion' => Aplicacion::getInstance()->resuelve('index.php')
        ]);
    }

    // Generar campos del formulario (con email)
    protected function generaCamposFormulario(&$datos)
    {
        $username = $datos['username'] ?? '';
        $email = $datos['email'] ?? '';

        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(
            ['username', 'email', 'password', 'password2'], 
            $this->errores, 
            'span', 
            ['class' => 'error']
        );

        $html = <<<EOF
        $htmlErroresGlobales
        <fieldset>
            <div>
                <label for="username">Nombre de usuario:</label>
                <input id="username" type="text" name="username" value="$username" required />
                {$erroresCampos['username']}
            </div>
            <div>
                <label for="email">Correo electrónico:</label>
                <input id="email" type="email" name="email" value="$email" required />
                {$erroresCampos['email']}
            </div>
            <div>
                <label for="password">Contraseña:</label>
                <input id="password" type="password" name="password" required />
                {$erroresCampos['password']}
            </div>
            <div>
                <label for="password2">Repite la contraseña:</label>
                <input id="password2" type="password" name="password2" required />
                {$erroresCampos['password2']}
            </div>
            <div>
                <button type="submit" name="registro">Registrarse</button>
            </div>
        </fieldset>
        EOF;
        return $html;
    }

    // Procesamiento del formulario (validar email y username único)
    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];

        // Validar username
        $username = trim($datos['username'] ?? '');
        if (empty($username) || mb_strlen($username) < 5) {
            $this->errores['username'] = 'El nombre de usuario debe tener al menos 5 caracteres.';
        }

        // Validar email
        $email = trim($datos['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errores['email'] = 'Correo electrónico inválido.';
        }

        // Validar contraseñas
        $password = trim($datos['password'] ?? '');
        $password2 = trim($datos['password2'] ?? '');
        if ($password !== $password2) {
            $this->errores['password2'] = 'Las contraseñas no coinciden.';
        }

        // Crear usuario si no hay errores
        if (count($this->errores) === 0) {
            $usuarioExistente = Usuario::buscaUsuario($username);
            if ($usuarioExistente) {
                $this->errores[] = "El usuario ya existe.";
            } else {
                $usuario = Usuario::crea($username, $password, $email);
                if ($usuario !== false) { // Asegurar que $usuario no sea false
                    $_SESSION['login'] = true;
                    $_SESSION['username'] = $username;
                    $_SESSION['roles'] = [$usuario->getRol()]; // Solo si $usuario es válido
                    //Aplicacion::redirige('/index.php');
                } else {
                    $this->errores[] = "Error al registrar el usuario. Verifica los datos.";
                }
            }
        }
    }
}
?>