<?php

require_once __DIR__.'/Formulario.php';



namespace es\ucm\fdi\aw;

class FormularioLogin extends Formulario {
    public function __construct() {
        parent::__construct('formLogin', [
            'action' => 'login.php',
            'class' => 'form-login'
        ]);
    }

    protected function generaCamposFormulario(&$datos) {
        $username = $datos['username'] ?? '';
        $errorGlobal = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['username', 'password'], $this->errores, 'span', ['class' => 'error']);

        $html = <<<EOS
        <fieldset>
            <legend>Acceso al sistema</legend>
            $errorGlobal
            <div>
                <label for="username">Nombre de usuario:</label>
                <input type="text" name="username" id="username" value="$username" required>
                {$erroresCampos['username']}
            </div>
            <div>
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required>
                {$erroresCampos['password']}
            </div>
            <div>
                <button type="submit" name="login">Entrar</button>
            </div>
        </fieldset>
        EOS;
        return $html;
    }

    protected function procesaFormulario(&$datos) {
        $username = trim($datos['username'] ?? '');
        $password = trim($datos['password'] ?? '');

        if (empty($username)) {
            $this->errores['username'] = 'El nombre de usuario no puede estar vacío';
        }
        if (empty($password)) {
            $this->errores['password'] = 'La contraseña no puede estar vacía';
        }

        if (count($this->errores) === 0) {
            $usuario = Usuario::login($username, $password);
            if ($usuario) {
                $_SESSION['login'] = true;
                $_SESSION['username'] = $usuario->getUsername();
                $_SESSION['usuario_rol'] = $usuario->getRol();
                header('Location: index.php');
                exit();
            } else {
                $this->errores[] = 'Usuario o contraseña incorrectos';
            }
        }
    }
}
?>