<?php
// Devuelve {exists:true/false} si el email existe en administradores
header('Content-Type: application/json');
if (!isset($_GET['email'])) {
    echo json_encode(['exists'=>false]);
    exit;
}
$email = $_GET['email'];
require_once '../bd/claseBD.php';
$db = new DB();
$pdo = $db->getPdo();
$stmt = $pdo->prepare('SELECT COUNT(*) FROM administradores WHERE email = ?');
$stmt->execute([$email]);
$exists = $stmt->fetchColumn() > 0;
echo json_encode(['exists'=>$exists]);
