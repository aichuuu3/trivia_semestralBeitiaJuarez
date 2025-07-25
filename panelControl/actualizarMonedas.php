<?php
session_start();
header('Content-Type: application/json');

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit();
}

// Incluir conexión a la base de datos
require_once '../bd/conexion.php';

try {
    // Obtener datos del POST
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['monedas_ganadas']) || !is_numeric($input['monedas_ganadas'])) {
        echo json_encode(['success' => false, 'message' => 'Datos de monedas inválidos']);
        exit();
    }
    
    $usuario_id = $_SESSION['usuario_id'];
    $monedas_ganadas = (int)$input['monedas_ganadas'];
    $categoria = $input['categoria'] ?? '';
    $porcentaje = $input['porcentaje'] ?? 0;
    $tiempo_total = $input['tiempo_total'] ?? 0;
    
    // Validar que las monedas ganadas sean positivas
    if ($monedas_ganadas < 0) {
        echo json_encode(['success' => false, 'message' => 'Las monedas ganadas no pueden ser negativas']);
        exit();
    }
    
    // Obtener monedas actuales del usuario
    $stmt = $pdo->prepare("SELECT monedas_totales FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit();
    }
    
    $monedas_actuales = (int)$usuario['monedas_totales'];
    $nuevas_monedas = $monedas_actuales + $monedas_ganadas;
    
    // Comenzar transacción
    $pdo->beginTransaction();
    
    try {
        // Actualizar monedas del usuario
        $stmt = $pdo->prepare("UPDATE usuarios SET monedas_totales = ? WHERE id = ?");
        $success = $stmt->execute([$nuevas_monedas, $usuario_id]);
        
        if (!$success) {
            throw new Exception('Error al actualizar monedas del usuario');
        }
        
        // Actualizar la sesión
        $_SESSION['usuario_monedas'] = $nuevas_monedas;
        
        // Registrar en historial de monedas
        $stmt = $pdo->prepare("INSERT INTO historial_monedas (usuario_id, monedas_ganadas, categoria, porcentaje, tiempo_total, fecha_ganadas) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$usuario_id, $monedas_ganadas, $categoria, $porcentaje, $tiempo_total]);
        
        // Confirmar transacción
        $pdo->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Monedas actualizadas correctamente',
            'monedas_anteriores' => $monedas_actuales,
            'monedas_ganadas' => $monedas_ganadas,
            'monedas_nuevas' => $nuevas_monedas
        ]);
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $pdo->rollback();
        throw $e;
    }
    
} catch (PDOException $e) {
    error_log("Error de base de datos en actualizarMonedas.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Error general en actualizarMonedas.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
}
?>
