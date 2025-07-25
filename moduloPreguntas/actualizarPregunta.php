<?php
require_once '../bd/claseBD.php';
header('Content-Type: application/json');
$response = [ 'status' => 'error', 'message' => '' ];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_pregunta'] ?? '';
    $pregunta = trim($_POST['pregunta'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $tema = trim($_POST['tema'] ?? '');
    $puntos = trim($_POST['puntos'] ?? '10');
    $respuesta_correcta = trim($_POST['respuesta_correcta'] ?? '');
    $respuesta_incorrecta_1 = trim($_POST['respuesta_incorrecta_1'] ?? '');
    $respuesta_incorrecta_2 = trim($_POST['respuesta_incorrecta_2'] ?? '');
    $respuesta_incorrecta_3 = trim($_POST['respuesta_incorrecta_3'] ?? '');
    if (empty($id) || empty($pregunta) || empty($categoria) || empty($tema) || empty($respuesta_correcta) || empty($respuesta_incorrecta_1)) {
        $response['message'] = 'Faltan datos obligatorios.';
        echo json_encode($response); exit;
    }
    $db = new DB();
    $pdo = $db->getPdo();
    try {
        $pdo->beginTransaction();
        $ok = $db->actualizarPregunta($id, $pregunta, $categoria, $tema, $puntos);
        if (!$ok) throw new Exception('Error al actualizar pregunta');
        // Eliminar respuestas anteriores
        $pdo->prepare("DELETE FROM respuestas WHERE id_pregunta=?")->execute([$id]);
        // Insertar respuestas nuevas
        $sqlR = "INSERT INTO respuestas (id_pregunta, texto_respuesta, es_correcta) VALUES (?, ?, ?)";
        $pdo->prepare($sqlR)->execute([$id, $respuesta_correcta, 1]);
        $pdo->prepare($sqlR)->execute([$id, $respuesta_incorrecta_1, 0]);
        if (!empty($respuesta_incorrecta_2)) $pdo->prepare($sqlR)->execute([$id, $respuesta_incorrecta_2, 0]);
        if (!empty($respuesta_incorrecta_3)) $pdo->prepare($sqlR)->execute([$id, $respuesta_incorrecta_3, 0]);
        $pdo->commit();
        $response['status'] = 'success';
        $response['message'] = 'Pregunta actualizada correctamente';
    } catch (Exception $e) {
        $pdo->rollBack();
        $response['message'] = 'Error al actualizar: ' . $e->getMessage();
    }
    echo json_encode($response); exit;
}
$response['message'] = 'Petición inválida';
echo json_encode($response);
