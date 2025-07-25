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

if (isset($_POST['categoria_id'])) {
    $categoria_id = (int)$_POST['categoria_id'];
    
    try {
        // Consulta para obtener temas que tienen preguntas en la categoría especificada
        $query = "SELECT DISTINCT t.id_tema, t.nombre_tema, t.descripcion 
                 FROM temas t 
                 INNER JOIN preguntas p ON t.id_tema = p.id_tema 
                 WHERE p.cod_categoria = :categoria_id AND p.activa = 1
                 ORDER BY t.nombre_tema ASC";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
        $stmt->execute();
        $temas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($temas)) {
            $response['status'] = 'success';
            $response['message'] = 'Temas cargados correctamente';
            $response['data'] = $temas;
        } else {
            $response['message'] = 'No se encontraron temas con preguntas activas para esta categoría';
        }
        
    } catch (Exception $e) {
        $response['message'] = 'Error al cargar temas: ' . $e->getMessage();
    }
    
} else {
    $response['message'] = 'ID de categoría no especificado';
}

// Enviar respuesta JSON
echo json_encode($response);
?>
