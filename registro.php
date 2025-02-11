<?php
require 'includes/config/database.php';

$db = conectarDB();

$nombre = '';
$email = '';
$password = '';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!$nombre) {
        $errores[] = "Debes añadir un nombre";
    }

    if (!$email) {
        $errores[] = "Debes añadir un email";
    }

    if (!$password) {
        $errores[] = "Debes añadir una contraseña";
    }

    // Revisar errores y si no existe otro usuario con el mismo email
    if (empty($errores)) {

        // Revisar que el email sea válido
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "Email inválido";
        }

        // Revisar si el email ya está registrado
        $query = "SELECT * FROM usuarios WHERE email = '$email'";
        $resultado = mysqli_query($db, $query);

        // hashear el password

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        if ($resultado->num_rows) {
            $errores[] = "El email ya está registrado";
        } else {
            $query = "INSERT INTO usuarios (nombre, email, password) VALUES ('$nombre', '$email', '$passwordHash')";
            $resultado = mysqli_query($db, $query);

            if ($resultado) {
                header('Location: /inicio-sesion.php');
            }
        }
    }

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - To-Do-List</title>
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
                    <a href="inicio-sesion.php" class="navegacion__enlace">
                        <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <main class="contenedor">
        <div class="auth-container"></div>
            <h1><i class="fas fa-user-plus"></i> Crear Cuenta</h1>

            <?php foreach($errores as $error): ?>
                <div class="alerta error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endforeach; ?>

            <form method="POST" class="formulario">
                <fieldset>
                    <legend>Información Personal</legend>

                    <div class="campo">
                        <label for="nombre">
                            <i class="fas fa-user"></i> Nombre
                        </label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo $nombre ?>" placeholder="Tu Nombre" required>
                    </div>

                    <div class="campo">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <input type="email" id="email" name="email" value="<?php echo $email ?>" placeholder="Tu Email" required>
                    </div>

                    <div class="campo">
                        <label for="password">
                            <i class="fas fa-lock"></i> Contraseña
                        </label>
                        <input type="password" id="password" name="password" placeholder="Tu Contraseña" required>
                    </div>
                </fieldset>

                <input type="submit" value="Crear Cuenta" class="btn">

                <div class="auth-enlaces">
                    <p>¿Ya tienes cuenta? <a href="inicio-sesion.php">Inicia sesión aquí</a></p>
                </div>
            </form>
        </div>
    </main>
</body>
</html>