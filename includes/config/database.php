<?php

function conectarDB() : mysqli{
    $db = new mysqli('localhost', 'root', '', 'abastoscheck_v5');

    if(!$db){
        echo 'No se pudo conectar a la base de datos';
        exit;
    }
    return $db;
}