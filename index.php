<?php
require 'includes/config/app.php';
require 'includes/config/funciones.php';
require 'includes/config/database.php';
$db = conectarDB();

session_start();

$errores = [];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="src/CSS/Normalize.css">
    <link rel="stylesheet" href="src/CSS/style.css">
    <title>To Do List</title>
</head>
<body>
<header class="header">
    <div class="contenedor__header">
        <div class="barra">
            <p class="logo">To Do List</p>

            <nav class="navegacion">
                <?php if (isset($_SESSION['login']) && $_SESSION['login']): ?>
                    <div class="usuario">
                        <div class="usuario__info">
                            <div class="usuario__iniciales">
                                <?php echo strtoupper(substr($_SESSION['usuario'], 0, 2)); ?>
                            </div>
                            <div class="usuario__detalles">
                                <p> <span>Usuario: </span>  <?php echo $_SESSION['nombre']; ?></p> <!-- Nombre de usuario -->
                                <p><span>Correo: </span> <?php echo $_SESSION['usuario']; ?></p> <!-- Correo electrónico -->
                                <a href="cerrar-sesion.php" class="navegacion__enlace">Cerrar Sesión</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="registro.php" class="navegacion__enlace">Registro</a>
                    <a href="inicio-sesion.php" class="navegacion__enlace">Inicio de Sesión</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>

<main class="contenedor main">
    <div class="barra__lsitas">
        <h2>Listas</h2>

        <div class="contenedor__btn">
            <button class="btn btn__crear">Crear Lista</button>
            <button class="btn btn__editar">Editar Lista</button>
            <button class="btn btn__eliminar">Eliminar Lista</button>
        </div>
    </div>

    <section class="lsitas contenedor">
        <div class="listas">
            <h2>Tareas pedendientes</h2>

            <div class="mensajes">
                <p>Tarea de Mateameticas</p>
                <p>Tarea de Programacion</p>
                <p>Tarea de Metodologias</p>
            </div>
        </div>
    </section>

</main>

</body>
</html>