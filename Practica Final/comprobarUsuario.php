<?php
require_once __DIR__.'/includes/config.php';
use es\ucm\fdi\aw\usuarios\Usuario;

$username = $_GET['user'] ?? '';

if (Usuario::buscaUsuario($username)) {
    echo "existe";
} else {
    echo "disponible";
}