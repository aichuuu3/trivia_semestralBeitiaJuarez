<?php
header('Content-Type: application/json');

try {
    // Incluir conexión a la base de datos
    require_once '../bd/conexion.php';
    
    // Consultar todas las imágenes disponibles para avatares
    $stmt = $pdo->prepare("SELECT id_imagen, nombre_archivo, nombre_display, tipo_imagen FROM imagenes WHERE activa = 1 AND tipo_imagen = 'avatar' ORDER BY nombre_display");
    $stmt->execute();
    $imagenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($imagenes) {
        echo json_encode([
            'status' => 'success',
            'data' => $imagenes,
            'message' => 'Imágenes cargadas correctamente'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'data' => [],
            'message' => 'No hay imágenes disponibles'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'data' => [],
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
}
?>
