<?php
require_once __DIR__.'/includes/config.php';
require_once __DIR__.'/includes/Usuario.php';

$formEnviado = isset($_POST['registro']);
if (!$formEnviado) {
    header('Location: registro.php');
    exit();
}

require_once __DIR__.'/includes/utils.php';

$erroresFormulario = [];

// Sanitizar y validar campos
$nombreUsuario = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$password2 = filter_input(INPUT_POST, 'password2', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Validaciones
if (!$nombreUsuario || empty(trim($nombreUsuario))) {
    $erroresFormulario['username'] = 'El nombre de usuario no puede estar vacío.';
} elseif (mb_strlen(trim($nombreUsuario)) < 5) {
    $erroresFormulario['username'] = 'El nombre debe tener al menos 5 caracteres.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erroresFormulario['email'] = 'Correo electrónico no válido.';
}

if (!$password || empty(trim($password))) {
    $erroresFormulario['password'] = 'La contraseña no puede estar vacía.';
} elseif (mb_strlen(trim($password)) < 5) {
    $erroresFormulario['password'] = 'La contraseña debe tener al menos 5 caracteres.';
}

if ($password !== $password2) {
    $erroresFormulario['password2'] = 'Las contraseñas no coinciden.';
}


if (count($erroresFormulario) === 0) {
   
    $usuarioExistente = Usuario::buscaUsuario($nombreUsuario);
    if ($usuarioExistente) {
        $erroresFormulario[] = "El nombre de usuario ya está en uso.";
    } else {
       
        $usuario = Usuario::crea($nombreUsuario, $password, $email, Usuario::CLIENTE_ROLE);
        if ($usuario) {
            $_SESSION['login'] = true;
            $_SESSION['username'] = $usuario->getUsername();
            $_SESSION['usuario_rol'] = $usuario->getRol();
            header('Location: index.php');
            exit();
        } else {
            $erroresFormulario[] = "Error al registrar el usuario.";
        }
    }
}


$tituloPagina = 'Registro';

$erroresGlobalesFormulario = generaErroresGlobalesFormulario($erroresFormulario);
$erroresCampos = generaErroresCampos(['username', 'email', 'password', 'password2'], $erroresFormulario);

$contenidoPrincipal = <<<EOS
<h1>Registro de usuario</h1>
$erroresGlobalesFormulario
<form action="procesarRegistro.php" method="POST">
<fieldset>
    <legend>Datos para el registro</legend>
    <div>
        <label for="username">Nombre de usuario:</label>
        <input id="username" type="text" name="username" value="$nombreUsuario" />
        {$erroresCampos['username']}
    </div>
    <div>
        <label for="email">Correo electrónico:</label>
        <input id="email" type="email" name="email" value="$email" />
        {$erroresCampos['email']}
    </div>
    <div>
        <label for="password">Contraseña:</label>
        <input id="password" type="password" name="password" />
        {$erroresCampos['password']}
    </div>
    <div>
        <label for="password2">Reintroduce la contraseña:</label>
        <input id="password2" type="password" name="password2" />
        {$erroresCampos['password2']}
    </div>
    <div>
        <button type="submit" name="registro">Registrar</button>
    </div>
</fieldset>
</form>
EOS;

require __DIR__.'/includes/vistas/plantillas/plantilla.php';
?>