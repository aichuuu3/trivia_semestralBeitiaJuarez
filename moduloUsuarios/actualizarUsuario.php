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
    // Obtener datos del formulario
    $id = intval($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $monedas = intval($_POST['monedas'] ?? 0);
    $categoria = intval($_POST['categoria'] ?? 0);
    
    // Validaciones
    if ($id <= 0 || empty($nombre) || empty($email) || $monedas < 0 || $categoria <= 0) {
        throw new Exception('Todos los campos son obligatorios y deben ser válidos');
    }
    
    // Validar que la categoría existe (1=Experto, 2=Novato, 3=Principiante)
    if (!in_array($categoria, [1, 2, 3])) {
        throw new Exception('Categoría no válida');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('El email no tiene un formato válido');
    }
    
    // Verificar si el email ya existe en otro usuario
    $sqlCheck = "SELECT id FROM usuarios WHERE email = :email AND id != :id";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->bindParam(':email', $email);
    $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtCheck->execute();
    
    if ($stmtCheck->rowCount() > 0) {
        throw new Exception('El email ya está registrado por otro usuario');
    }
    
    // Actualizar usuario
    $sql = "UPDATE usuarios 
            SET nombre = :nombre, email = :email, monedas_totales = :monedas, cod_categoria = :categoria 
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':monedas', $monedas, PDO::PARAM_INT);
    $stmt->bindParam(':categoria', $categoria, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    $resultado = $stmt->execute();
    
    if ($resultado && $stmt->rowCount() > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Usuario actualizado exitosamente'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se pudo actualizar el usuario o no hubo cambios'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
