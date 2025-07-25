<?php
require_once 'bd/claseBD.php';
$db = new DB();

echo "Testing insertarImagen directly...\n";

try {
    $pdo = $db->getPdo();
    $query = $pdo->prepare("INSERT INTO imagenes (nombre_archivo, nombre_display, ruta_completa, extension, tamaño_kb, tipo_imagen, descripcion, activa) 
                          VALUES (:nombre_archivo, :nombre_display, :ruta_completa, :extension, :tamaño_kb, 'avatar', 'Avatar personalizado subido por administrador', 1)");
    
    $query->bindParam(":nombre_archivo", $nombre_archivo = 'test_direct.jpg');
    $query->bindParam(":nombre_display", $nombre_display = 'Test Direct');
    $query->bindParam(":ruta_completa", $ruta_completa = '../img/test_direct.jpg');
    $query->bindParam(":extension", $extension = 'jpg');
    $query->bindParam(":tamaño_kb", $tamaño_kb = 100);
    
    $result = $query->execute();
    
    if ($result) {
        echo "✅ Inserción directa exitosa\n";
        
        // Limpiar
        $pdo->exec("DELETE FROM imagenes WHERE nombre_archivo = 'test_direct.jpg'");
        echo "✅ Limpieza exitosa\n";
    } else {
        echo "❌ Error en inserción directa\n";
        print_r($query->errorInfo());
    }
    
} catch (PDOException $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}
?>
