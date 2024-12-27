<?php

// importar la base de datos
require 'includes/config/database.php';
$db = conectarDB();

$errores = [];

// Auntecacion de usuarios
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $email = mysqli_real_escape_string($db, filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) );
    // mysqli_real_escape_string() es una función que nos permite escapar los caracteres especiales de una cadena de texto para que no se puedan ejecutar comandos maliciosos en la base de datos.
    // filter_var() es una función que nos permite validar si una cadena de texto cumple con un formato especifico.
    // FILTER_VALIDATE_EMAIL es una constante que nos permite validar si una cadena de texto es un email.

    $password = mysqli_real_escape_string($db, $_POST['password']);
    // En este caso no validamos el password porque no es necesario, ya que el password no puede contener caracteres especiales.

    if (!$email) {
        $errores[] = "El email es obligatorio o no es valido";
    }

    if (!$password) {
        $errores[] = "El password es obligatorio";
    }

    if (empty($errores)) {
        // Revisar si el usuario existe
        $query = "SELECT * FROM usuarios WHERE email = '${email}'";
        $resultado = mysqli_query($db, $query);

        if ($resultado->num_rows) {
            // El num_rows es una propiedad de la clase mysqli_result que nos permite saber cuantos registros se encontraron en una consulta.
            // Y en este caso nos permite saber si se encontró un usuario con el email que ingreso el usuario.
            // Revisar si el password es correcto
            $usuario = mysqli_fetch_assoc($resultado);
            // mysqli_fetch_assoc() es una función que nos permite obtener un arreglo asociativo con los datos de un registro de la base de datos.

            // Verificar si el password es correcto o no
            $auth = password_verify($password, $usuario['password']);
            // password_verify() es una función que nos permite verificar si una contraseña en texto plano es igual a una contraseña encriptada.
            // La función recibe dos parámetros, el primero es la contraseña en texto plano y el segundo es la contraseña encriptada.
            // La función nos devuelve un booleano, true si las contraseñas son iguales y false si no lo son.

            if ($auth) {
                // El usuario esta autenticado
                session_start();
                // session_start() es una función que nos permite iniciar una sesión en PHP.

                // Llenar el arreglo de la sesion
                $_SESSION['usuario'] = $usuario['email'];

                // Obtener nombre de usuario de la base de datos para mostrarlo en el header
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['id'] = $usuario['id']; // Añadir el ID del usuario

                // $_SESSION es una variable super global que nos permite guardar información del usuario en la sesión.
                $_SESSION['login'] = true;
                // En este caso guardamos el email del usuario en la sesión para saber que usuario esta autenticado.

                header('Location: /'); // Cambiar a la página principal
            } else {
                $errores[] = "El password es incorrecto";
            }

        } else {
            $errores[] = "El Usuario no existe";
        }

    }


}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="src/CSS/Normalize.css">
    <link rel="stylesheet" href="src/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="contenedor__header">
            <div class="barra">
                <a href="/" class="logo">To Do List</a>
                <nav class="navegacion">
                    <a href="registro.php" class="navegacion__enlace">
                        <i class="fas fa-user-plus"></i> Crear cuenta
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <main class="contenedor">
        <div class="auth-container"></div>
            <h1><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</h1>

            <?php foreach($errores as $error): ?>
                <div class="alerta error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endforeach; ?>

            <form method="POST" class="formulario">
                <fieldset>
                    <legend>Accede a tu cuenta</legend>

                    <div class="campo">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Correo Electrónico
                        </label>
                        <input type="email" id="email" name="email" placeholder="Tu Email" required>
                    </div>

                    <div class="campo">
                        <label for="password">
                            <i class="fas fa-lock"></i> Contraseña
                        </label>
                        <input type="password" id="password" name="password" placeholder="Tu Contraseña" required>
                    </div>
                </fieldset>

                <input type="submit" value="Iniciar Sesión" class="btn">

                <div class="auth-enlaces">
                    <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
                </div>
            </form>
        </div>
    </main>
</body>
</html>