<?php
session_start();
require_once '../bd/claseBD.php';

if (!isset($_SESSION['colaborador_id'])) {
    die('No autorizado');
}

$colaborador_id = $_SESSION['colaborador_id'];
$db = new DB();

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Solo manejar subida de archivo nuevo
    if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['avatar_file'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $tamaño_bytes = $archivo['size'];
        $tamaño_kb = round($tamaño_bytes / 1024);
        
        // Validaciones
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $extensiones_permitidas)) {
            $mensaje = 'Solo se permiten archivos de imagen (JPG, PNG, GIF, WEBP)';
            $tipo_mensaje = 'error';
        } else if ($tamaño_bytes > 5 * 1024 * 1024) {
            $mensaje = 'El archivo es demasiado grande. Máximo 5MB.';
            $tipo_mensaje = 'error';
        } else {
            // Procesar archivo
            $nombre_archivo = 'avatar_colaborador_' . $colaborador_id . '_' . time() . '.' . $extension;
            $ruta_destino = '../img/' . $nombre_archivo;
            
            if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
                // Actualizar directamente en BD sin tabla imagenes
                $pdo = $db->getPdo();
                $stmt = $pdo->prepare("UPDATE colaboradores SET avatar = ? WHERE id = ?");
                $resultado = $stmt->execute([$nombre_archivo, $colaborador_id]);
                
                if ($resultado) {
                    $mensaje = '✅ Avatar actualizado correctamente!';
                    $tipo_mensaje = 'success';
                } else {
                    $mensaje = '❌ Error al actualizar avatar en base de datos';
                    $tipo_mensaje = 'error';
                    unlink($ruta_destino); // Eliminar archivo
                }
            } else {
                $mensaje = '❌ Error al subir archivo';
                $tipo_mensaje = 'error';
            }
        }
    }
    // Manejar selección de avatar predefinido
    else if (isset($_POST['avatar_predefinido'])) {
        $avatar_seleccionado = $_POST['avatar_predefinido'];
        $pdo = $db->getPdo();
        $stmt = $pdo->prepare("UPDATE colaboradores SET avatar = ? WHERE id = ?");
        $resultado = $stmt->execute([$avatar_seleccionado, $colaborador_id]);
        
        if ($resultado) {
            $mensaje = '✅ Avatar predefinido actualizado correctamente!';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = '❌ Error al actualizar avatar predefinido';
            $tipo_mensaje = 'error';
        }
    }
}

// Obtener datos actuales
$colaborador = $db->obtenerColaborador($colaborador_id);
$pdo = $db->getPdo();
$stmt = $pdo->query("SELECT nombre_archivo, nombre_display FROM imagenes WHERE tipo_imagen = 'avatar' AND activa = 1");
$avatares_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Avatar - Test Simple</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .mensaje { padding: 15px; border-radius: 5px; margin: 20px 0; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .avatar-actual { text-align: center; margin: 20px 0; }
        .avatar-actual img { width: 150px; height: 150px; border-radius: 50%; border: 3px solid #ddd; }
        .seccion { margin: 30px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .avatar-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 15px; margin: 15px 0; }
        .avatar-option { text-align: center; cursor: pointer; }
        .avatar-option img { width: 80px; height: 80px; border-radius: 50%; border: 2px solid transparent; }
        .avatar-option input[type="radio"]:checked + img { border-color: #007bff; }
        .file-input { margin: 15px 0; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>🎭 Test Simple - Cambiar Avatar Colaborador</h1>
    
    <?php if ($mensaje): ?>
        <div class="mensaje <?php echo $tipo_mensaje; ?>">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>
    
    <div class="avatar-actual">
        <h3>Avatar Actual</h3>
        <img src="../img/<?php echo htmlspecialchars($colaborador['avatar']); ?>?v=<?php echo time(); ?>" alt="Avatar actual">
        <p><strong><?php echo htmlspecialchars($colaborador['nombre']); ?></strong></p>
        <p>Archivo: <?php echo htmlspecialchars($colaborador['avatar']); ?></p>
    </div>
    
    <div class="seccion">
        <h3>📋 Opción 1: Seleccionar Avatar Predefinido</h3>
        <form method="post">
            <div class="avatar-grid">
                <?php foreach($avatares_disponibles as $avatar): ?>
                    <label class="avatar-option">
                        <input type="radio" name="avatar_predefinido" value="<?php echo htmlspecialchars($avatar['nombre_archivo']); ?>" 
                               <?php echo ($colaborador['avatar'] === $avatar['nombre_archivo']) ? 'checked' : ''; ?>>
                        <img src="../img/<?php echo htmlspecialchars($avatar['nombre_archivo']); ?>?v=<?php echo time(); ?>" 
                             alt="<?php echo htmlspecialchars($avatar['nombre_display']); ?>">
                        <br><small><?php echo htmlspecialchars($avatar['nombre_display']); ?></small>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="btn">Usar Avatar Seleccionado</button>
        </form>
    </div>
    
    <div class="seccion">
        <h3>📸 Opción 2: Subir Imagen Nueva</h3>
        <form method="post" enctype="multipart/form-data">
            <div class="file-input">
                <label for="avatar_file">Seleccionar archivo:</label>
                <input type="file" id="avatar_file" name="avatar_file" accept="image/*" required>
            </div>
            <p><small>Formatos permitidos: JPG, PNG, GIF, WEBP. Máximo 5MB.</small></p>
            <button type="submit" class="btn">Subir Nueva Imagen</button>
        </form>
    </div>
    
    <hr>
    <p><a href="configuracionColaboradores.php?tab=edit">← Volver a Configuración Normal</a></p>
    
    <script>
        // Auto-refrescar imagen después de 2 segundos si hay mensaje de éxito
        <?php if ($tipo_mensaje === 'success'): ?>
        setTimeout(function() {
            const img = document.querySelector('.avatar-actual img');
            const currentSrc = img.src;
            img.src = currentSrc.split('?')[0] + '?v=' + new Date().getTime();
        }, 2000);
        <?php endif; ?>
    </script>
</body>
</html>
