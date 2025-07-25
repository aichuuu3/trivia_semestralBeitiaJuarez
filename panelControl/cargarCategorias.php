<?php
require_once '../bd/conexion.php';

// Establecer cabecera para JSON
header('Content-Type: application/json');

// Respuesta JSON estándar
$response = [
    'status' => 'error',
    'message' => '',
    'data' => []
];

try {
    // Consulta para obtener categorías
    $query = "SELECT id_categoria, nombre_categoria FROM categoria ORDER BY id_categoria ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($categorias)) {
        $response['status'] = 'success';
        $response['message'] = 'Categorías cargadas correctamente';
        $response['data'] = $categorias;
    } else {
        $response['message'] = 'No se encontraron categorías';
    }
    
} catch (Exception $e) {
    $response['message'] = 'Error al cargar categorías: ' . $e->getMessage();
}

// Enviar respuesta JSON
echo json_encode($response);
?>
