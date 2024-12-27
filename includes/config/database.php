<?php
// Conectar a la base de datos
function conectarDB() : mysqli {
    $db = mysqli_connect('localhost', 'root', 'root', 'todolist');

    if(!$db) {
        error_log("Error de conexión: " . mysqli_connect_error());
        echo "Error en la conexión";
        exit;
    }

    // Establecer charset
    mysqli_set_charset($db, "utf8");
    
    return $db;
}