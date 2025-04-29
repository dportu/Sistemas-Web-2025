<?php

function resuelve($path = ''){ 
    $url = '';  
    $app = \es\ucm\fdi\aw\Aplicacion::getInstance();
    $url = $app->resuelve($path);
    return $url;
}
