<?php
require_once '../bd/claseBD.php';

$db = new DB();
$pdo = $db->getPdo();

echo "<h2>🧹 Limpieza y Verificación de Avatares</h2>";

// 1. Verificar archivos en directorio img
echo "<h3>📁 Archivos en directorio /img:</h3>";
$img_dir = '../img/';
$archivos_fisicos = array_diff(scandir($img_dir), array('.', '..'));
foreach($archivos_fisicos as $archivo) {
    if (strpos($archivo, 'avatar_colaborador_') === 0) {
        echo "- $archivo<br>";
    }
}

// 2. Verificar registros en BD de avatares de colaboradores
echo "<h3>💾 Avatares de colaboradores en BD:</h3>";
$stmt = $pdo->query("SELECT * FROM imagenes WHERE nombre_archivo LIKE 'avatar_colaborador_%'");
$avatares_bd = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($avatares_bd as $avatar) {
    $existe_archivo = file_exists($img_dir . $avatar['nombre_archivo']);
    echo "- {$avatar['nombre_archivo']} - BD: ✅ | Archivo: " . ($existe_archivo ? "✅" : "❌") . "<br>";
    
    if (!$existe_archivo) {
        echo "&nbsp;&nbsp;&nbsp;⚠️ Archivo faltante, eliminando registro de BD...<br>";
        $del_stmt = $pdo->prepare("DELETE FROM imagenes WHERE id_imagen = ?");
        $del_stmt->execute([$avatar['id_imagen']]);
    }
}

// 3. Verificar colaboradores
echo "<h3>👥 Estado de colaboradores:</h3>";
$stmt = $pdo->query("SELECT id, nombre, avatar FROM colaboradores");
$colaboradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($colaboradores as $colaborador) {
    $avatar_existe = file_exists($img_dir . $colaborador['avatar']);
    echo "- ID: {$colaborador['id']} | Nombre: {$colaborador['nombre']} | Avatar: {$colaborador['avatar']} | Archivo: " . ($avatar_existe ? "✅" : "❌") . "<br>";
    
    if (!$avatar_existe && $colaborador['avatar'] !== 'avatar.png') {
        echo "&nbsp;&nbsp;&nbsp;🔄 Resetear a avatar.png...<br>";
        $update_stmt = $pdo->prepare("UPDATE colaboradores SET avatar = 'avatar.png' WHERE id = ?");
        $update_stmt->execute([$colaborador['id']]);
    }
}

// 4. Limpiar archivos huérfanos
echo "<h3>🗑️ Limpiando archivos huérfanos de colaboradores:</h3>";
foreach($archivos_fisicos as $archivo) {
    if (strpos($archivo, 'avatar_colaborador_') === 0) {
        // Verificar si existe en BD
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM imagenes WHERE nombre_archivo = ?");
        $stmt->execute([$archivo]);
        $existe_en_bd = $stmt->fetchColumn() > 0;
        
        // Verificar si algún colaborador lo usa
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM colaboradores WHERE avatar = ?");
        $stmt->execute([$archivo]);
        $usado_por_colaborador = $stmt->fetchColumn() > 0;
        
        if (!$existe_en_bd && !$usado_por_colaborador) {
            echo "- Eliminando archivo huérfano: $archivo<br>";
            unlink($img_dir . $archivo);
        } else {
            echo "- Archivo válido: $archivo (BD: " . ($existe_en_bd ? "✅" : "❌") . " | Usado: " . ($usado_por_colaborador ? "✅" : "❌") . ")<br>";
        }
    }
}

echo "<hr>";
echo "<h3>✅ Limpieza completada</h3>";
echo "<a href='configuracionColaboradores.php?tab=edit'>← Volver a configuración</a>";
?>
