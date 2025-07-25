<?php
require_once '../bd/claseBD.php';
$db = new DB();
$pdo = $db->getPdo();

// Selecciona todos los administradores
$stmt = $pdo->query('SELECT id, password FROM administradores');
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($admins as $admin) {
    // Solo actualiza si la contraseña NO está hasheada (no empieza por $2y$)
    if (strpos($admin['password'], '$2y$') !== 0) {
        $hash = password_hash($admin['password'], PASSWORD_DEFAULT);
        $update = $pdo->prepare('UPDATE administradores SET password = ? WHERE id = ?');
        $update->execute([$hash, $admin['id']]);
    }
}
echo 'Contraseñas actualizadas correctamente.';
?>
