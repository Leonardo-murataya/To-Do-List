<?php
$db = mysqli_connect('localhost', 'root', 'root', 'ToDoList');
if (!$db) {
    die("No se puede conectar a MySQL: " . mysqli_connect_error());
}