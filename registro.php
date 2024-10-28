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
        $query = "SELECT * FROM usuarios WHERE email = '${email}'";
        $resultado = mysqli_query($db, $query);

        // hashear el password

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        if ($resultado->num_rows) {
            $errores[] = "El email ya está registrado";
        } else {
            $query = "INSERT INTO usuarios (nombre, email, password) VALUES ('${nombre}', '${email}', '${passwordHash}')";
            $resultado = mysqli_query($db, $query);

            if ($resultado) {
                header('Location: /inicio-sesion.php');
            }
        }
    }

}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resgistro - To-Do-List</title>
    <link rel="stylesheet" href="src/CSS/Normalize.css">
</head>
<body>

    <main>
        <h1>Crear una cuenta</h1>

        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach; ?>

        <form method="POST" class="formulario" action="registro.php">
            <fieldset>
                <legend>Información Personal</legend>

                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo $nombre ?>" placeholder="Tu Nombre">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $email ?>" placeholder="Tu Email">

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Tu Contraseña">
            </fieldset>

            <input type="submit" value="Crear Cuenta" class="boton boton-verde">
        </form>
    </main>

    <footer>
        <p>¿Ya tienes una cuenta? <a href="inicio-sesion.php">Iniciar Sesión</a></p>
    </footer>

</body>
</html>