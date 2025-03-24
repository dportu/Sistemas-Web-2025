<?php


require_once __DIR__.'/Formulario.php';


class FormularioRegistro extends Formulario {
    public function __construct() {
        parent::__construct('formRegistro', [
            'action' => 'registro.php',
            'class' => 'form-registro'
        ]);
    }

    protected function generaCamposFormulario(&$datos) {
        $errorGlobal = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['username', 'email', 'password', 'password2'], $this->errores, 'span', ['class' => 'error']);

        $html = <<<EOS
        <h1>Registro de usuario</h1>
        $errorGlobal
        <form method="POST">
            <fieldset>
                <legend>Datos para el registro</legend>
                <div>
                    <label for="username">Nombre de usuario:</label>
                    <input id="username" type="text" name="username" required>
                    {$erroresCampos['username']}
                </div>
                <div>
                    <label for="email">Correo electrónico:</label>
                    <input id="email" type="email" name="email" required>
                    {$erroresCampos['email']}
                </div>
                <div>
                    <label for="password">Contraseña:</label>
                    <input id="password" type="password" name="password" required>
                    {$erroresCampos['password']}
                </div>
                <div>
                    <label for="password2">Reintroduce la contraseña:</label>
                    <input id="password2" type="password" name="password2" required>
                    {$erroresCampos['password2']}
                </div>
                <div>
                    <button type="submit" name="registro">Registrar</button>
                </div>
            </fieldset>
        </form>
        EOS;
        return $html;
    }

    protected function procesaFormulario(&$datos) {
        $username = trim($datos['username'] ?? '');
        $email = trim($datos['email'] ?? '');
        $password = trim($datos['password'] ?? '');
        $password2 = trim($datos['password2'] ?? '');

        // Validaciones...
        if (empty($username)) {
            $this->errores['username'] = 'El nombre de usuario no puede estar vacío';
        }
        if (empty($email)) {
            $this->errores['email'] = 'El correo electrónico no puede estar vacío';
        }
        if (empty($password)) {
            $this->errores['password'] = 'La contraseña no puede estar vacía';
        }
        if ($password !== $password2) {
            $this->errores['password2'] = 'Las contraseñas no coinciden';
        }

        if (count($this->errores) === 0) {
            $usuario = Usuario::crea($username, $password, $email, 'cliente');
            if ($usuario) {
                $_SESSION['login'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['usuario_rol'] = 'cliente';
                header('Location: index.php');
                exit();
            } else {
                $this->errores[] = 'Error al registrar el usuario';
            }
        }
    }
}
?>