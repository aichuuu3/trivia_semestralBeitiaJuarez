<?php
require_once '../bd/claseBD.php';
header('Content-Type: application/json');
$response = [ 'status' => 'error', 'message' => '' ];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_pregunta'] ?? '';
    if (empty($id)) {
        $response['message'] = 'ID de pregunta requerido.';
        echo json_encode($response); exit;
    }
    $db = new DB();
    $ok = $db->eliminarPregunta($id);
    if ($ok) {
        $response['status'] = 'success';
        $response['message'] = 'Pregunta eliminada correctamente';
    } else {
        $response['message'] = 'Error al eliminar la pregunta';
    }
    echo json_encode($response); exit;
}
$response['message'] = 'Petición inválida';
echo json_encode($response);
