<?php
require_once 'conexion.php';

try {
    // Verificar si la columna avatar ya existe
    $stmt = $pdo->query("SHOW COLUMNS FROM colaboradores LIKE 'avatar'");
    $column_exists = $stmt->rowCount() > 0;
    
    if (!$column_exists) {
        // Añadir la columna avatar
        $pdo->exec("ALTER TABLE colaboradores ADD COLUMN avatar varchar(255) DEFAULT 'avatar.png' AFTER password");
        echo "✅ Columna 'avatar' añadida exitosamente a la tabla colaboradores\n";
        
        // Actualizar registros existentes para que tengan avatar.png
        $pdo->exec("UPDATE colaboradores SET avatar = 'avatar.png' WHERE avatar IS NULL OR avatar = ''");
        echo "✅ Registros existentes actualizados con avatar por defecto\n";
    } else {
        echo "ℹ️ La columna 'avatar' ya existe en la tabla colaboradores\n";
    }
    
    // Verificar el resultado
    $stmt = $pdo->query("SELECT id, nombre, avatar FROM colaboradores");
    $colaboradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n📊 Estado actual de colaboradores:\n";
    foreach ($colaboradores as $colaborador) {
        echo "- ID: {$colaborador['id']}, Nombre: {$colaborador['nombre']}, Avatar: {$colaborador['avatar']}\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
