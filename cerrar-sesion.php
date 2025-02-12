<?php
session_start();
$_SESSION = [];
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <script>
        // Limpiar localStorage antes de redireccionar
        localStorage.clear();
        // Regresar al usuario al Index
        window.location.href = '/index.php';
    </script>
</head>
</html>