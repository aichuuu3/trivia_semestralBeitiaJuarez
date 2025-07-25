<?php
// Test de subida de archivos
echo "Testing upload functionality...\n";

// Verificar directorio img
if (!is_dir('img')) {
    echo "❌ Directorio img no existe\n";
} else if (!is_writable('img')) {
    echo "❌ Directorio img no tiene permisos de escritura\n";
} else {
    echo "✅ Directorio img existe y es escribible\n";
}

// Verificar método insertarImagen
require_once 'bd/claseBD.php';
$db = new DB();

// Test básico de inserción
$test_result = $db->insertarImagen(
    'test_avatar.jpg', 
    'Test Avatar', 
    '../img/test_avatar.jpg', 
    'jpg', 
    100
);

if ($test_result) {
    echo "✅ Método insertarImagen funciona correctamente\n";
    
    // Limpiar el test
    $pdo = $db->getPdo();
    $pdo->exec("DELETE FROM imagenes WHERE nombre_archivo = 'test_avatar.jpg'");
    echo "✅ Test limpiado\n";
} else {
    echo "❌ Error en método insertarImagen\n";
}

echo "\n✅ Prueba completada\n";
?>
