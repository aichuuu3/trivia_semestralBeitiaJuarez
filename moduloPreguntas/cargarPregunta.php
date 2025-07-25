<?php
header('Content-Type: application/json');

try {
    // Incluir conexión a la base de datos
    require_once '../bd/conexion.php';
    
    // Verificar que se recibió el ID
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('ID de pregunta no especificado');
    }
    
    $id_pregunta = (int)$_GET['id'];
    
    // Consulta para obtener los datos de la pregunta
    $queryPregunta = "SELECT p.id_pregunta, p.texto_pregunta, p.puntos, p.cod_categoria, p.id_tema, p.activa
                      FROM preguntas p 
                      WHERE p.id_pregunta = :id_pregunta";
    
    $stmtPregunta = $pdo->prepare($queryPregunta);
    $stmtPregunta->bindParam(':id_pregunta', $id_pregunta, PDO::PARAM_INT);
    $stmtPregunta->execute();
    
    $pregunta = $stmtPregunta->fetch(PDO::FETCH_ASSOC);
    
    if (!$pregunta) {
        throw new Exception('Pregunta no encontrada');
    }
    
    // Consulta para obtener las respuestas
    $queryRespuestas = "SELECT id_respuesta, texto_respuesta, es_correcta 
                        FROM respuestas 
                        WHERE id_pregunta = :id_pregunta 
                        ORDER BY es_correcta DESC, id_respuesta";
    
    $stmtRespuestas = $pdo->prepare($queryRespuestas);
    $stmtRespuestas->bindParam(':id_pregunta', $id_pregunta, PDO::PARAM_INT);
    $stmtRespuestas->execute();
    
    $respuestas = $stmtRespuestas->fetchAll(PDO::FETCH_ASSOC);
    
    // Agregar respuestas al array de pregunta
    $pregunta['respuestas'] = $respuestas;
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'pregunta' => $pregunta
    ]);

} catch (Exception $e) {
    // Respuesta de error
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
