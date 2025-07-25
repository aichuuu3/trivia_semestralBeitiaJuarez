<?php
header('Content-Type: application/json');
require_once '../bd/claseBD.php';
$email = $_GET['email'] ?? '';
$db = new DB();
$pdo = $db->getPdo();
$stmt = $pdo->prepare('SELECT id FROM colaboradores WHERE email = ? AND activo = 1');
$stmt->execute([$email]);
$exists = $stmt->fetch() ? true : false;
echo json_encode(['exists' => $exists]);
