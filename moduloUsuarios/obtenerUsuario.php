<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../bd/conexion.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID de usuario requerido'
    ]);
    exit;
}

try {
    $id = intval($_GET['id']);
    
    // Consulta para obtener un usuario específico
    $sql = "SELECT u.id, u.nombre, u.email, u.password, u.monedas_totales, u.fecha_registro, u.activo,
                   u.cod_categoria, c.nombre_categoria
            FROM usuarios u
            LEFT JOIN categoria c ON u.cod_categoria = c.id_categoria
            WHERE u.id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario) {
        echo json_encode([
            'status' => 'success',
            'data' => $usuario
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Usuario no encontrado'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
?>
