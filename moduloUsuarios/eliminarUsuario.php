<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../bd/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Método no permitido'
    ]);
    exit;
}

try {
    // Obtener ID del usuario a eliminar
    $id = intval($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        throw new Exception('ID de usuario inválido');
    }
    
    // Verificar que el usuario existe
    $sqlCheck = "SELECT id FROM usuarios WHERE id = :id";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtCheck->execute();
    
    if ($stmtCheck->rowCount() === 0) {
        throw new Exception('Usuario no encontrado');
    }
    
    // Eliminar usuario (borrado lógico - marcar como inactivo)
    $sql = "UPDATE usuarios SET activo = 0 WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    $resultado = $stmt->execute();
    
    if ($resultado && $stmt->rowCount() > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Usuario eliminado exitosamente'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se pudo eliminar el usuario'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
