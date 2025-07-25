<?php
require_once '../bd/claseBD.php';
header('Content-Type: application/json');
$response = [ 'status' => 'error', 'message' => '', 'pregunta' => null ];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'] ?? '';
    $texto = $_GET['texto'] ?? '';
    $db = new DB();
    $pdo = $db->getPdo();
    if (!empty($id) && is_numeric($id)) {
        // Buscar pregunta y respuestas por ID
        $query = $pdo->prepare("SELECT p.*, 
            GROUP_CONCAT(CASE WHEN r.es_correcta = 1 THEN r.texto_respuesta END) as respuesta_correcta,
            GROUP_CONCAT(CASE WHEN r.es_correcta = 0 THEN r.texto_respuesta END ORDER BY r.id_respuesta ASC SEPARATOR '|') as respuestas_incorrectas
            FROM preguntas p
            LEFT JOIN respuestas r ON p.id_pregunta = r.id_pregunta
            WHERE p.id_pregunta = :id
            GROUP BY p.id_pregunta");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $pregunta = $query->fetch(PDO::FETCH_ASSOC);
        if ($pregunta) {
            // Separar respuestas incorrectas
            $incorrectas = isset($pregunta['respuestas_incorrectas']) ? explode('|', $pregunta['respuestas_incorrectas']) : [];
            $pregunta['respuesta_incorrecta_1'] = $incorrectas[0] ?? '';
            $pregunta['respuesta_incorrecta_2'] = $incorrectas[1] ?? '';
            $pregunta['respuesta_incorrecta_3'] = $incorrectas[2] ?? '';
            $response['status'] = 'success';
            $response['pregunta'] = $pregunta;
        } else {
            $response['message'] = 'No se encontró la pregunta con ese ID.';
        }
    } elseif (!empty($texto)) {
        $query = $pdo->prepare("SELECT * FROM preguntas WHERE texto_pregunta LIKE :texto LIMIT 1");
        $like = "%$texto%";
        $query->bindParam(':texto', $like);
        $query->execute();
        $pregunta = $query->fetch(PDO::FETCH_ASSOC);
        if ($pregunta) {
            $response['status'] = 'success';
            $response['pregunta'] = $pregunta;
        } else {
            $response['message'] = 'No se encontró ninguna pregunta con ese texto.';
        }
    } else {
        $response['message'] = 'Parámetros insuficientes.';
    }
    echo json_encode($response); exit;
}

$response['message'] = 'Petición inválida';
echo json_encode($response);
