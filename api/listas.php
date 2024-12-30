<?php
require '../includes/config/database.php';
session_start();
$userId = $_SESSION['id'] ?? 0;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

if (!$userId) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$db = conectarDB();
$method = $_SERVER['REQUEST_METHOD'];

// GET: Obtener listas y sus tareas
if ($method === 'GET') {
    $sql = "SELECT l.id, l.nombre, 
            GROUP_CONCAT(t.id, ':::', t.descripcion, ':::', t.completada SEPARATOR '|||') as tareas 
            FROM listas l 
            LEFT JOIN tareas t ON l.id = t.lista_id 
            WHERE l.usuario_id = {$userId} 
            GROUP BY l.id";
    $res = mysqli_query($db, $sql);
    $listas = [];
    
    while ($row = mysqli_fetch_assoc($res)) {
        $lista = [
            'id' => $row['id'],
            'nombre' => $row['nombre'],
            'tareas' => []
        ];
        
        if ($row['tareas']) {
            $tareas = explode('|||', $row['tareas']);
            foreach ($tareas as $tarea) {
                list($id, $descripcion, $completada) = explode(':::', $tarea);
                if ($id) {
                    $lista['tareas'][] = [
                        'id' => (int)$id,
                        'texto' => $descripcion,
                        'completada' => $completada == '1'
                    ];
                }
            }
        }
        
        $listas[] = $lista;
    }
    
    echo json_encode($listas);
    exit;
}

// POST: Crear nueva lista o tarea
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Crear lista
    if (isset($data['nombre'])) {
        $nombre = mysqli_real_escape_string($db, $data['nombre']);
        $sql = "INSERT INTO listas (usuario_id, nombre) VALUES ({$userId}, '{$nombre}')";
        if (mysqli_query($db, $sql)) {
            echo json_encode(['id' => mysqli_insert_id($db), 'nombre' => $nombre]);
        } else {
            echo json_encode(['error' => 'Error al crear lista']);
        }
        exit;
    }
    // Crear tarea
    else if (isset($data['lista_id'], $data['descripcion'])) {
        $listaId = (int)$data['lista_id'];
        $descripcion = mysqli_real_escape_string($db, $data['descripcion']);
        
        // Verificar que la lista pertenezca al usuario
        $checkSql = "SELECT id FROM listas WHERE id = {$listaId} AND usuario_id = {$userId}";
        $checkRes = mysqli_query($db, $checkSql);
        
        if (mysqli_num_rows($checkRes) > 0) {
            $sql = "INSERT INTO tareas (lista_id, descripcion, completada) VALUES ({$listaId}, '{$descripcion}', 0)";
            if (mysqli_query($db, $sql)) {
                $id = mysqli_insert_id($db);
                echo json_encode([
                    'id' => $id,
                    'texto' => $descripcion,
                    'completada' => false
                ]);
            } else {
                echo json_encode(['error' => 'Error al crear tarea']);
            }
        } else {
            echo json_encode(['error' => 'Lista no encontrada']);
        }
        exit;
    }
    
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

// PUT: Actualizar lista o tarea
if ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)($_GET['id'] ?? 0);
    
    if (isset($data['nombre'])) { // Cambiado para manejar actualización de lista
        $nombre = mysqli_real_escape_string($db, $data['nombre']);
        $sql = "UPDATE listas SET nombre = '{$nombre}' 
                WHERE id = {$id} AND usuario_id = {$userId}";
                
        if (mysqli_query($db, $sql)) {
            echo json_encode(['success' => true, 'id' => $id, 'nombre' => $nombre]);
        } else {
            echo json_encode(['error' => 'Error al actualizar lista: ' . mysqli_error($db)]);
        }
    } 
    else if (isset($data['descripcion'])) { // Para actualizar tareas
        $listaId = (int)($_GET['lista_id'] ?? 0);
        $descripcion = mysqli_real_escape_string($db, $data['descripcion']);
        
        $sql = "UPDATE tareas t 
                INNER JOIN listas l ON t.lista_id = l.id 
                SET t.descripcion = '{$descripcion}' 
                WHERE t.id = {$id} AND l.usuario_id = {$userId}";
                
        if (mysqli_query($db, $sql)) {
            echo json_encode(['success' => true, 'id' => $id, 'texto' => $descripcion]);
        } else {
            echo json_encode(['error' => 'Error al actualizar tarea: ' . mysqli_error($db)]);
        }
    }
    exit;
}

// DELETE: Eliminar lista o tarea
if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    
    if (isset($_GET['tipo']) && $_GET['tipo'] === 'tarea') {
        $listaId = (int)($_GET['lista_id'] ?? 0);
        $sql = "DELETE t FROM tareas t 
                INNER JOIN listas l ON t.lista_id = l.id 
                WHERE t.id = {$id} AND l.usuario_id = {$userId}";
    } else {
        // Primero eliminar tareas asociadas
        $sql1 = "DELETE t FROM tareas t 
                WHERE t.lista_id = {$id} AND EXISTS (
                    SELECT 1 FROM listas l 
                    WHERE l.id = t.lista_id AND l.usuario_id = {$userId}
                )";
        mysqli_query($db, $sql1);
        
        // Luego eliminar la lista
        $sql = "DELETE FROM listas WHERE id = {$id} AND usuario_id = {$userId}";
    }
    
    if (mysqli_query($db, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Error al eliminar: ' . mysqli_error($db)]);
    }
    exit;
}

echo json_encode(['error' => 'Método no soportado']);
?>