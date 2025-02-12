<?php
require 'includes/config/database.php';
require 'includes/config/funciones.php';

session_start();
$db = conectarDB();

// Solo mantenemos los errores para el manejo de sesión
$errores = [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Do List</title>
    <link rel="stylesheet" href="src/CSS/Normalize.css">
    <link rel="stylesheet" href="src/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body <?php if(isset($_SESSION['login']) && $_SESSION['login']) echo 'data-user-id="'.$_SESSION['id'].'"'; ?>>
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
                                    <p><span>Usuario:</span> <?php echo $_SESSION['nombre']; ?></p>
                                    <p><span>Correo:</span> <?php echo $_SESSION['usuario']; ?></p>
                                    <a href="cerrar-sesion.php" class="navegacion__enlace rojo">
                                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="navegacion">
                            <a href="registro.php" class="navegacion__enlace">
                                <i class="fas fa-user-plus"></i> Registro
                            </a>
                            <a href="inicio-sesion.php" class="navegacion__enlace azul">
                                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                            </a>
                        </div>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <main class="contenedor">
    <div class="listas-header">
            <h2>Listas</h2>

            <?php if (!isset($_SESSION['login']) || !$_SESSION['login']): ?>
                <div class="alerta alerta-precaucion">
                    <p><i class="fas fa-exclamation-triangle"></i> No estás conectado a la base de datos. Los datos se guardarán en el almacenamiento local.</p>
                </div>
            <?php endif; ?>
            
            <button id="nuevaLista" class="btn btn-nueva-lista">
                <i class="fas fa-plus"></i> Nueva Lista
            </button>
        </div>

        <div id="listas" class="listas-grid">
            <!-- Las listas se insertarán aquí dinámicamente via JavaScript -->
        </div>
    </main>

    <!-- Modales -->
    <div id="modal" class="modal">
        <div class="modal-contenido">
            <span class="cerrar">&times;</span>
            <h2>Nueva Lista</h2>
            <form id="formLista" autocomplete="off">
                <div class="campo">
                    <label for="titulo">Título de la Lista:</label>
                    <input type="text" id="titulo" name="titulo" required minlength="1">
                </div>
                <button type="submit" class="btn">Crear Lista</button>
            </form>
        </div>
    </div>

    <div id="modalTarea" class="modal">
        <div class="modal-contenido">
            <span class="cerrar">&times;</span>
            <h2>Nueva Tarea</h2>
            <form id="formTarea">
                <input type="hidden" id="listaId">
                <div class="campo">
                    <label for="descripcionTarea">Descripción de la Tarea:</label>
                    <input type="text" id="descripcionTarea" name="descripcionTarea" required>
                </div>
                <button type="submit" class="btn">Crear Tarea</button>
            </form>
        </div>
    </div>

    <div id="modalEditar" class="modal">
        <div class="modal-contenido">
            <span class="cerrar">&times;</span>
            <h2 id="modalEditarTitulo">Editar</h2>
            <form id="formEditar">
                <input type="hidden" id="elementoId">
                <input type="hidden" id="tipoElemento">
                <div class="campo">
                    <label for="nuevoTexto">Nuevo texto:</label>
                    <input type="text" id="nuevoTexto" name="nuevoTexto" required>
                </div>
                <button type="submit" class="btn">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <script src="src/JS/app.js"></script>
</body>
</html>