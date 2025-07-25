<?php
require_once 'bd/claseBD.php';
$db = new DB();
$pdo = $db->getPdo();

echo "Verificando administrador edgar.juarez@reichmind.com\n";
$stmt = $pdo->prepare('SELECT * FROM administradores WHERE email = ?');
$stmt->execute(['edgar.juarez@reichmind.com']);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin) {
    echo "✅ Administrador encontrado:\n";
    print_r($admin);
} else {
    echo "❌ No se encontró el administrador\n";
}

echo "\nListando todos los administradores:\n";
$stmt = $pdo->query('SELECT id, nombre, email, activo FROM administradores');
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($admins as $admin) {
    echo "ID: {$admin['id']}, Nombre: {$admin['nombre']}, Email: {$admin['email']}, Activo: {$admin['activo']}\n";
}
?>
