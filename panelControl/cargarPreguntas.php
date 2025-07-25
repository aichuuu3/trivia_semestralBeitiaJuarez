<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../bd/conexion.php';

try {
    if (!isset($_POST['tema_id']) || !isset($_POST['categoria_id'])) {
        throw new Exception('Parámetros requeridos: tema_id y categoria_id');
    }
    
    $tema_id = intval($_POST['tema_id']);
    $categoria_id = intval($_POST['categoria_id']);
    
    // Obtener preguntas aleatorias del tema y categoría especificados
    $sql = "SELECT p.id_pregunta, p.texto_pregunta, p.puntos
            FROM preguntas p
            WHERE p.id_tema = :tema_id 
            AND p.cod_categoria = :categoria_id 
            AND p.activa = 1
            ORDER BY RAND()";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':tema_id', $tema_id, PDO::PARAM_INT);
    $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($preguntas)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se encontraron preguntas para este tema y categoría'
        ]);
        exit;
    }
    
    // Para cada pregunta, obtener sus respuestas (también aleatorizadas)
    $preguntasCompletas = [];
    
    foreach ($preguntas as $pregunta) {
        $sqlRespuestas = "SELECT id_respuesta, texto_respuesta, es_correcta
                         FROM respuestas 
                         WHERE id_pregunta = :id_pregunta
                         ORDER BY RAND()";
        
        $stmtRespuestas = $pdo->prepare($sqlRespuestas);
        $stmtRespuestas->bindParam(':id_pregunta', $pregunta['id_pregunta'], PDO::PARAM_INT);
        $stmtRespuestas->execute();
        
        $respuestas = $stmtRespuestas->fetchAll(PDO::FETCH_ASSOC);
        
        $preguntasCompletas[] = [
            'id_pregunta' => $pregunta['id_pregunta'],
            'texto_pregunta' => $pregunta['texto_pregunta'],
            'puntos' => $pregunta['puntos'],
            'respuestas' => $respuestas
        ];
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $preguntasCompletas,
        'total_preguntas' => count($preguntasCompletas)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
