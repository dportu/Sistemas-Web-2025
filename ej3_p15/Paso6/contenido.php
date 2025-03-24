<?php

require_once __DIR__.'/includes/config.php';


$tituloPagina = 'Contenido Exclusivo';
$contenidoPrincipal = '';

if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
    // Contenido para usuarios registrados
    $contenidoPrincipal .= <<<EOS
    <h1>Contenido Exclusivo</h1>
    <p>Este contenido es exclusivo para los usuarios registrados</p>
    <h2>Coches más rápidos</h2>
    <ul>
        <li><a href="#coche1">Koenigsegg Jesko Absolut</a></li>
        <li><a href="#coche2">Hennessey Venom F5</a></li>
        <li><a href="#coche3">Bugatti Chiron Super Sport 300+</a></li>
        <li><a href="#coche4">SSC Tuatara</a></li>
    </ul>

    <div class="coche" id="coche1">
        <h3>Koenigsegg Jesko Absolut</h3>
        <img src="./img/1.webp" alt="Koenigsegg Jesko Absolut">
        <p>Velocidad máxima: 532 km/h.</p>
    </div>

    <div class="coche" id="coche2">
        <h3>Hennessey Venom F5</h3>
        <img src="./img/2.webp" alt="Hennessey Venom F5">
        <p>Velocidad máxima: 500 km/h.</p>
    </div>

    <div class="coche" id="coche3">
        <h3>Bugatti Chiron Super Sport 300+</h3>
        <img src="./img/3.webp" alt="Bugatti Chiron Super Sport 300+">
        <p>Velocidad máxima: 490 km/h.</p>
    </div>

    <div class="coche" id="coche4">
        <h3>SSC Tuatara</h3>
        <img src="./img/4.webp" alt="SSC Tuatara">
        <p>Velocidad máxima: 480 km/h.</p>
    </div>
EOS;
} else {
    // Contenido para usuarios no registrados
    $contenidoPrincipal .= <<<EOS
    <h1>Contenido Restringido</h1>
    <p>Lo sentimos, este contenido está disponible solo para usuarios registrados.</p>
    <p>Por favor, <a href='login.php'>inicia sesión</a> para ver el contenido completo.</p>
    <div class='contenido-publico'>
        <h2>Contenido público</h2>
        <p>Mientras tanto, puedes explorar:</p>
        <ul>
            <li>Noticias del mundo automotriz</li>
            <li>Historia de las marcas</li>
            <li>Consejos de mantenimiento</li>
        </ul>
    </div>
EOS;
}

require __DIR__.'/includes/vistas/plantillas/plantilla.php';