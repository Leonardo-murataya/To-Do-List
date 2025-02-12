<?php
session_start();
require 'includes/config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

$db = conectarDB();
$usuario_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = json_decode(file_get_contents('php://input'), true);
    
    if ($datos === null) {
        echo json_encode(['error' => 'Datos inválidos']);
        exit;
    }

    // Obtener IDs de listas existentes
    $query = "SELECT id FROM listas WHERE usuario_id = $usuario_id";
    $resultado = mysqli_query($db, $query);
    $listasExistentes = [];
    while ($row = mysqli_fetch_assoc($resultado)) {
        $listasExistentes[] = $row['id'];
    }

    foreach ($datos as $lista) {
        $titulo = mysqli_real_escape_string($db, $lista['titulo']);
        $lista_id = $lista['id'];
        
        // Verificar si la lista existe
        if (in_array($lista_id, $listasExistentes)) {
            // Actualizar lista existente
            $query = "UPDATE listas SET titulo = '$titulo' WHERE id = $lista_id AND usuario_id = $usuario_id";
            mysqli_query($db, $query);
            // Remover de listasExistentes para saber cuáles borrar después
            $listasExistentes = array_diff($listasExistentes, [$lista_id]);
        } else {
            // Insertar nueva lista
            $query = "INSERT INTO listas (id, titulo, usuario_id) VALUES ($lista_id, '$titulo', $usuario_id)";
            mysqli_query($db, $query);
        }

        // Obtener IDs de tareas existentes para esta lista
        $query = "SELECT id FROM tareas WHERE lista_id = $lista_id";
        $resultado = mysqli_query($db, $query);
        $tareasExistentes = [];
        while ($row = mysqli_fetch_assoc($resultado)) {
            $tareasExistentes[] = $row['id'];
        }

        if (isset($lista['tareas']) && is_array($lista['tareas'])) {
            foreach ($lista['tareas'] as $tarea) {
                $texto = mysqli_real_escape_string($db, $tarea['texto']);
                $tarea_id = $tarea['id'];
                $completada = $tarea['completada'] ? 1 : 0;
                
                if (in_array($tarea_id, $tareasExistentes)) {
                    // Actualizar tarea existente
                    $query = "UPDATE tareas SET texto = '$texto', completada = $completada 
                             WHERE id = $tarea_id AND lista_id = $lista_id";
                    mysqli_query($db, $query);
                    // Remover de tareasExistentes
                    $tareasExistentes = array_diff($tareasExistentes, [$tarea_id]);
                } else {
                    // Insertar nueva tarea
                    $query = "INSERT INTO tareas (id, texto, completada, lista_id) 
                             VALUES ($tarea_id, '$texto', $completada, $lista_id)";
                    mysqli_query($db, $query);
                }
            }
        }

        // Eliminar tareas que ya no existen
        if (!empty($tareasExistentes)) {
            $tareasEliminar = implode(',', $tareasExistentes);
            mysqli_query($db, "DELETE FROM tareas WHERE id IN ($tareasEliminar) AND lista_id = $lista_id");
        }
    }

    // Eliminar listas que ya no existen
    if (!empty($listasExistentes)) {
        $listasEliminar = implode(',', $listasExistentes);
        mysqli_query($db, "DELETE FROM tareas WHERE lista_id IN ($listasEliminar)");
        mysqli_query($db, "DELETE FROM listas WHERE id IN ($listasEliminar) AND usuario_id = $usuario_id");
    }
    
    echo json_encode(['success' => true]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Esta consulta SQL combina (JOIN) las tablas de listas y tareas
    // para obtener toda la información en una sola consulta
    $query = "SELECT l.id as lista_id, l.titulo, 
              t.id as tarea_id, t.texto, t.completada 
              FROM listas l 
              LEFT JOIN tareas t ON l.id = t.lista_id 
              WHERE l.usuario_id = $usuario_id";
              
    $resultado = mysqli_query($db, $query);
    
    $listas = [];
    while ($row = mysqli_fetch_assoc($resultado)) {
        // Por cada lista encontrada, crea un nuevo array con su información
        if (!isset($listas[$row['lista_id']])) {
            $listas[$row['lista_id']] = [
                'id' => (int)$row['lista_id'],
                'titulo' => $row['titulo'],
                'tareas' => []
            ];
        }
        
        // Si la lista tiene tareas, las agrega al array de tareas de esa lista
        if ($row['tarea_id']) {
            $listas[$row['lista_id']]['tareas'][] = [
                'id' => (int)$row['tarea_id'],
                'texto' => $row['texto'],
                'completada' => (bool)$row['completada']
            ];
        }
    }
    
    // Convierte el resultado en JSON y lo envía al cliente
    echo json_encode(array_values($listas));
}
