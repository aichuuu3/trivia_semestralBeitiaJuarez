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
    
    // Obtener monedas actuales del usuario, partidas ganadas y partidas fallidas
    $stmt = $pdo->prepare("SELECT monedas_totales, partidas_ganadas, partidas_fallidas FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit();
    }
    
    $monedas_actuales = (int)$usuario['monedas_totales'];
    $partidas_ganadas_actuales = (int)($usuario['partidas_ganadas'] ?? 0);
    $partidas_fallidas_actuales = (int)($usuario['partidas_fallidas'] ?? 0);
    $nuevas_monedas = $monedas_actuales + $monedas_ganadas;
    
    // Verificar si es una partida ganada (100% de aciertos) o fallida
    $es_partida_ganada = ($porcentaje >= 100);
    $es_partida_fallida = ($porcentaje < 100);
    
    $nuevas_partidas_ganadas = $partidas_ganadas_actuales + ($es_partida_ganada ? 1 : 0);
    $nuevas_partidas_fallidas = $partidas_fallidas_actuales + ($es_partida_fallida ? 1 : 0);
    
    // Comenzar transacción
    $pdo->beginTransaction();
    
    try {
        // Actualizar monedas, partidas ganadas y partidas fallidas del usuario
        $stmt = $pdo->prepare("UPDATE usuarios SET monedas_totales = ?, partidas_ganadas = ?, partidas_fallidas = ? WHERE id = ?");
        $success = $stmt->execute([$nuevas_monedas, $nuevas_partidas_ganadas, $nuevas_partidas_fallidas, $usuario_id]);
        
        if (!$success) {
            throw new Exception('Error al actualizar datos del usuario');
        }
        
        // Actualizar la sesión
        $_SESSION['usuario_monedas'] = $nuevas_monedas;
        
        // Registrar en historial de monedas incluyendo si fue partida ganada
        $stmt = $pdo->prepare("INSERT INTO historial_monedas (usuario_id, monedas_ganadas, categoria, porcentaje, tiempo_total, partida_ganada, fecha_ganadas) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$usuario_id, $monedas_ganadas, $categoria, $porcentaje, $tiempo_total, $es_partida_ganada ? 1 : 0]);
        
        // Confirmar transacción
        $pdo->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Datos actualizados correctamente',
            'monedas_anteriores' => $monedas_actuales,
            'monedas_ganadas' => $monedas_ganadas,
            'monedas_nuevas' => $nuevas_monedas,
            'partidas_ganadas_anteriores' => $partidas_ganadas_actuales,
            'partidas_ganadas_nuevas' => $nuevas_partidas_ganadas,
            'partidas_fallidas_anteriores' => $partidas_fallidas_actuales,
            'partidas_fallidas_nuevas' => $nuevas_partidas_fallidas,
            'es_partida_ganada' => $es_partida_ganada,
            'es_partida_fallida' => $es_partida_fallida
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
