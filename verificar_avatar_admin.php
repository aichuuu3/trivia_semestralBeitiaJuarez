<?php
require_once 'bd/claseBD.php';
$db = new DB();
$pdo = $db->getPdo();

echo "Verificando estructura de la tabla administradores...\n";

// Verificar si la columna avatar existe
$stmt = $pdo->query("DESCRIBE administradores");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

$avatarExists = false;
foreach ($columns as $column) {
    if ($column['Field'] === 'avatar') {
        $avatarExists = true;
        break;
    }
}

if (!$avatarExists) {
    echo "❌ La columna 'avatar' no existe. Añadiéndola...\n";
    
    try {
        // Añadir la columna avatar
        $pdo->exec("ALTER TABLE administradores ADD COLUMN avatar VARCHAR(255) DEFAULT 'avatar.png' AFTER password");
        echo "✅ Columna 'avatar' añadida correctamente.\n";
    } catch (PDOException $e) {
        echo "❌ Error al añadir la columna: " . $e->getMessage() . "\n";
    }
} else {
    echo "✅ La columna 'avatar' ya existe.\n";
}

// Verificar datos del administrador
echo "\nVerificando datos del administrador...\n";
$stmt = $pdo->query("SELECT id, nombre, email, avatar FROM administradores");
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($admins as $admin) {
    echo "ID: {$admin['id']}, Nombre: {$admin['nombre']}, Email: {$admin['email']}, Avatar: {$admin['avatar']}\n";
}

echo "\n✅ Verificación completada.\n";
?>
