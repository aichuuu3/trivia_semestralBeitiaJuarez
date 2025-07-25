<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../bd/conexion.php';

try {
    // Consulta para obtener usuarios con información de categoría
    $sql = "SELECT u.id, u.nombre, u.email, u.password, u.monedas_totales, u.fecha_registro, u.activo,
                   c.nombre_categoria
            FROM usuarios u
            LEFT JOIN categoria c ON u.cod_categoria = c.id_categoria
            WHERE u.activo = 1
            ORDER BY u.id ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => 'success',
        'data' => $usuarios
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
