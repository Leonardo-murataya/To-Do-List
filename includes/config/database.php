<?php
function conectarDB() {
    $db_host = getenv('DB_HOST') ?: 'localhost';
    $db_user = getenv('DB_USER') ?: 'todouser';
    $db_pass = getenv('DB_PASSWORD') ?: 'todopassword';
    $db_name = getenv('DB_NAME') ?: 'tododb';

    $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

    if(!$db) {
        echo "Error: No se pudo conectar a MySQL.";
        echo "errno de depuración: " . mysqli_connect_errno();
        echo "error de depuración: " . mysqli_connect_error();
        exit;
    }

    return $db;
}
?>