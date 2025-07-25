<?php
session_start();
header('Content-Type: application/json');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuario no autenticado'
    ]);
    exit;
}

$userId = $_SESSION['usuario_id'];

// Obtener datos del JSON enviado
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode([
        'success' => false,
        'message' => 'Datos no válidos'
    ]);
    exit;
}

$categoriaCompletada = $input['categoria'] ?? '';
$porcentaje = floatval($input['porcentaje'] ?? 0);

try {
    // Incluir conexión a la base de datos
    require_once '../bd/conexion.php';
    
    // Verificar que completó con 100% de aciertos
    if ($porcentaje != 100) {
        echo json_encode([
            'success' => true,
            'actualizado' => false,
            'message' => 'Se requiere 100% de aciertos para subir de nivel'
        ]);
        exit;
    }
    
    // Obtener nivel actual del usuario
    $stmt = $pdo->prepare("SELECT cod_categoria FROM usuarios WHERE id = ?");
    $stmt->execute([$userId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        throw new Exception('Usuario no encontrado');
    }
    
    $nivelActualId = intval($usuario['cod_categoria']);
    
    // Obtener información de categorías para conversión
    $stmtCat = $pdo->prepare("SELECT id_categoria, nombre_categoria FROM categoria ORDER BY id_categoria ASC");
    $stmtCat->execute();
    $categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
    
    // Crear mapeo de categorías
    $categoriasMap = [];
    $categoriasNombre = [];
    foreach ($categorias as $cat) {
        $categoriasMap[$cat['nombre_categoria']] = $cat['id_categoria'];
        $categoriasNombre[$cat['id_categoria']] = $cat['nombre_categoria'];
    }
    
    $nivelActualNombre = $categoriasNombre[$nivelActualId] ?? '';
    $categoriaCompletadaId = $categoriasMap[$categoriaCompletada] ?? 0;
    
    // Verificar si puede subir de nivel
    $puedeSubir = false;
    $nuevoNivelId = $nivelActualId;
    $nuevoNivelNombre = $nivelActualNombre;
    
    // Lógica de progresión: 
    // Principiante (3) -> Novato (2) -> Experto (1)
    if ($nivelActualId == 3 && $categoriaCompletada == 'Novato') {
        // De Principiante a Novato
        $puedeSubir = true;
        $nuevoNivelId = 2;
        $nuevoNivelNombre = 'Novato';
    } elseif ($nivelActualId == 2 && $categoriaCompletada == 'Experto') {
        // De Novato a Experto
        $puedeSubir = true;
        $nuevoNivelId = 1;
        $nuevoNivelNombre = 'Experto';
    }
    
    if (!$puedeSubir) {
        echo json_encode([
            'success' => true,
            'actualizado' => false,
            'message' => 'No cumples los requisitos para subir de nivel'
        ]);
        exit;
    }
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    // Actualizar nivel del usuario
    $stmtUpdate = $pdo->prepare("UPDATE usuarios SET cod_categoria = ? WHERE id = ?");
    $stmtUpdate->execute([$nuevoNivelId, $userId]);
    
    // Registrar en el historial de niveles (opcional - crear tabla si no existe)
    try {
        $stmtHistorial = $pdo->prepare("
            INSERT INTO historial_niveles (id_usuario, nivel_anterior, nivel_nuevo, categoria_completada, porcentaje_obtenido, fecha_cambio)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmtHistorial->execute([$userId, $nivelActualNombre, $nuevoNivelNombre, $categoriaCompletada, $porcentaje]);
    } catch (PDOException $e) {
        // Si la tabla no existe, crear una nota en el log pero continuar
        error_log("Tabla historial_niveles no existe o error al insertar: " . $e->getMessage());
    }
    
    // Confirmar transacción
    $pdo->commit();
    
    // Actualizar sesión
    $_SESSION['usuario_nivel'] = $nuevoNivelNombre;
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'actualizado' => true,
        'nivelAnterior' => $nivelActualNombre,
        'nivelNuevo' => $nuevoNivelNombre,
        'message' => "¡Felicidades! Has subido de nivel de $nivelActualNombre a $nuevoNivelNombre"
    ]);
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Error al actualizar nivel: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor'
    ]);
}
?>
