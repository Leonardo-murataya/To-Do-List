<?php
// Conectar a la base de datos
$db = mysqli_connect('localhost', 'root', 'root', 'ToDoList');
// Verificar la conexión
if (!$db) {
    die("No se puede conectar a MySQL: " . mysqli_connect_error());
}