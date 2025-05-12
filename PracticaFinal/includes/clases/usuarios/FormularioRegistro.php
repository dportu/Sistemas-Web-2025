<?php
namespace es\ucm\fdi\aw\usuarios;

use es\ucm\fdi\aw\Formulario;

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
        
        $errorGlobal
        
            <fieldset>
                <legend>Datos para el registro</legend>
                <div>
                    <label for="username">Nombre de usuario:</label>
                    <input required id="username" type="text" class="texto-card" name="username">
                    {$erroresCampos['username']}
                    <span id="userOK">&#x2714;</span> 
                    <span id="userMal">&#x274C;</span>
                    {$erroresCampos['username']}
                </div>
                <div>
                    <label for="email">Correo electrónico:</label>
                    <input required id="email" type="email" class="texto-card" name="email">
                    <span id="correoOK">&#x2714;</span> 
                    <span id="correoMal">&#x274C;</span>
                    {$erroresCampos['email']}
                </div>
                <div>
                    <label for="password">Contraseña:</label>
                    <input id="password" type="password" class="texto-card" name="password" required>
                    {$erroresCampos['password']}
                </div>
                <div>
                    <label for="password2">Reintroduce la contraseña:</label>
                    <input id="password2" type="password" class="texto-card" name="password2" required>
                    {$erroresCampos['password2']}
                </div>
                <div>
                    <button type="submit" name="registro">Registrar</button>
                </div>
            </fieldset>
        
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
            if(!Usuario::buscaUsuario($username)) { //sirve asi?
                $usuario = Usuario::crea($username, $password, $email, 'cliente' , 0); //jurao que deberiamos meter la sal pero funciona igualmente??

                if ($usuario) {
                    $_SESSION['login'] = true;
                    $_SESSION['username'] = $username;
                    $_SESSION['rol'] = 'cliente';
                    
                    header('Location: index.php');
                    exit();
                } else {
                    $this->errores[] = 'Error al registrar el usuario';
                }
            }
            else {
                $this->errores[] = 'Error, el usuario ya existe';
            }
        }
    }
}
?>
