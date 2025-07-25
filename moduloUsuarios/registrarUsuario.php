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
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Todos los usuarios nuevos empiezan como principiantes
    $cod_categoria = 3; // Principiante
    $monedasIniciales = 100; // 100 monedas iniciales
    
    // Validaciones
    if (empty($nombre) || empty($email) || empty($password) || empty($confirm_password)) {
        throw new Exception('Todos los campos son obligatorios');
    }
    
    if ($password !== $confirm_password) {
        throw new Exception('Las contraseñas no coinciden');
    }
    
    if (strlen($password) < 6) {
        throw new Exception('La contraseña debe tener al menos 6 caracteres');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('El email no tiene un formato válido');
    }
    
    // Verificar si el email ya existe
    $sqlCheck = "SELECT id FROM usuarios WHERE email = :email";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->bindParam(':email', $email);
    $stmtCheck->execute();
    
    if ($stmtCheck->rowCount() > 0) {
        throw new Exception('El email ya está registrado');
    }
    
    // Hashear la contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insertar usuario
    $sql = "INSERT INTO usuarios (nombre, email, password, cod_categoria, monedas_totales, fecha_registro, activo) 
            VALUES (:nombre, :email, :password, :cod_categoria, :monedas_totales, NOW(), 1)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $passwordHash);
    $stmt->bindParam(':cod_categoria', $cod_categoria);
    $stmt->bindParam(':monedas_totales', $monedasIniciales);
    
    $stmt->execute();
    
    // Obtener el ID del usuario creado
    $usuarioId = $pdo->lastInsertId();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Usuario registrado exitosamente',
        'data' => [
            'id' => $usuarioId,
            'nombre' => $nombre,
            'email' => $email,
            'monedas_iniciales' => $monedasIniciales
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
