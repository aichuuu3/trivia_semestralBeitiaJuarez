<?php
// Archivo de prueba para verificar el avatar

// Simular datos de sesión para la prueba
$test_usuario_id = 1;
$test_usuario_nombre = "Ana García";

// Incluir conexión a la base de datos
require_once '../bd/conexion.php';

// Obtener el avatar del usuario desde la base de datos
$usuario_avatar = 'avatar.png'; // Valor por defecto
try {
    $stmt = $pdo->prepare("SELECT avatar FROM usuarios WHERE id = ?");
    $stmt->execute([$test_usuario_id]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($resultado && !empty($resultado['avatar'])) {
        $usuario_avatar = $resultado['avatar'];
    }
} catch (PDOException $e) {
    // En caso de error, usar avatar por defecto
    $usuario_avatar = 'avatar.png';
    echo "Error de base de datos: " . $e->getMessage();
}

$ruta_avatar = "../img/" . $usuario_avatar;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Avatar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 500px;
            margin: 0 auto;
        }
        .avatar-test {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-image: url('<?php echo $ruta_avatar; ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 20px auto;
            border: 3px solid #ddd;
        }
        .info {
            text-align: center;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h2>🧪 Test de Avatar</h2>
        
        <div class="info">
            <strong>Usuario de prueba:</strong> <?php echo htmlspecialchars($test_usuario_nombre); ?>
        </div>
        
        <div class="info">
            <strong>Avatar en BD:</strong> <?php echo htmlspecialchars($usuario_avatar); ?>
        </div>
        
        <div class="info">
            <strong>Ruta completa:</strong> <?php echo htmlspecialchars($ruta_avatar); ?>
        </div>
        
        <div class="info">
            <strong>Archivo existe:</strong> <?php echo file_exists($ruta_avatar) ? '✅ Sí' : '❌ No'; ?>
        </div>
        
        <div class="avatar-test"></div>
        
        <div class="info">
            <strong>Estado:</strong> 
            <?php if (file_exists($ruta_avatar)): ?>
                ✅ Avatar cargado correctamente
            <?php else: ?>
                ❌ Archivo de avatar no encontrado
            <?php endif; ?>
        </div>
        
        <hr>
        
        <h3>📊 Información adicional:</h3>
        <ul style="text-align: left;">
            <li><strong>Usuario ID:</strong> <?php echo $test_usuario_id; ?></li>
            <li><strong>Conexión BD:</strong> <?php echo isset($pdo) ? '✅ Conectado' : '❌ Error'; ?></li>
            <li><strong>Carpeta img:</strong> <?php echo is_dir('../img') ? '✅ Existe' : '❌ No existe'; ?></li>
        </ul>
        
        <h3>🖼️ Imágenes disponibles:</h3>
        <ul style="text-align: left;">
            <?php
            $img_dir = '../img/';
            if (is_dir($img_dir)) {
                $files = scandir($img_dir);
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..' && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
                        echo "<li>📷 " . htmlspecialchars($file) . "</li>";
                    }
                }
            }
            ?>
        </ul>
    </div>
</body>
</html>
