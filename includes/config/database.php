<?php
// Conectar a la base de datos
function conectarDB() : mysqli {
    $db = mysqli_connect('localhost', 'root', 'root', 'todolist');

    if(!$db) {
        echo "Error en la conexión";
        exit;
    }

    return $db;
}