<?php
header('Content-Type: application/json');
session_start();

try {
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['usuario_id'])) {
        throw new Exception('Usuario no autenticado');
    }
    
    // Incluir conexión a la base de datos
    require_once '../bd/conexion.php';
    
    $usuario_id = $_POST['usuario_id'] ?? null;
    $tipo = $_POST['tipo'] ?? null;
    
    // Verificar que el usuario coincida con la sesión
    if ($usuario_id != $_SESSION['usuario_id']) {
        throw new Exception('No autorizado para cambiar este avatar');
    }
    
    if ($tipo === 'galeria') {
        // Cambiar avatar por uno de la galería
        $avatar_seleccionado = $_POST['avatar'] ?? null;
        
        if (!$avatar_seleccionado) {
            throw new Exception('No se especificó el avatar a seleccionar');
        }
        
        // Verificar que el archivo existe en la tabla de imágenes
        $stmt = $pdo->prepare("SELECT nombre_archivo FROM imagenes WHERE nombre_archivo = ? AND activa = 1 AND tipo_imagen = 'avatar'");
        $stmt->execute([$avatar_seleccionado]);
        $imagen_existe = $stmt->fetch();
        
        if (!$imagen_existe) {
            throw new Exception('La imagen seleccionada no existe o no está disponible');
        }
        
        // Verificar que el archivo físico existe
        $ruta_imagen = "../img/" . $avatar_seleccionado;
        if (!file_exists($ruta_imagen)) {
            throw new Exception('El archivo de imagen no existe en el servidor');
        }
        
        // Actualizar el avatar del usuario
        $stmt = $pdo->prepare("UPDATE usuarios SET avatar = ? WHERE id = ?");
        $stmt->execute([$avatar_seleccionado, $usuario_id]);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Avatar actualizado correctamente',
            'avatar' => $avatar_seleccionado
        ]);
        
    } elseif ($tipo === 'archivo') {
        // Subir archivo personalizado
        if (!isset($_FILES['archivo_avatar']) || $_FILES['archivo_avatar']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('No se recibió el archivo o hubo un error en la subida');
        }
        
        $archivo = $_FILES['archivo_avatar'];
        
        // Validar tipo de archivo
        $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipo_archivo = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($tipo_archivo, $tipos_permitidos)) {
            throw new Exception('Tipo de archivo no permitido. Use JPG, PNG o GIF');
        }
        
        // Validar tamaño (5MB máximo)
        if ($archivo['size'] > 5 * 1024 * 1024) {
            throw new Exception('El archivo es muy grande. Máximo 5MB permitido');
        }
        
        // Generar nombre único para el archivo
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombre_archivo = 'avatar_user_' . $usuario_id . '_' . time() . '.' . $extension;
        $ruta_destino = "../img/" . $nombre_archivo;
        
        // Mover archivo a la carpeta de destino
        if (!move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
            throw new Exception('Error al guardar el archivo en el servidor');
        }
        
        // Agregar imagen a la tabla de imágenes
        $stmt = $pdo->prepare("INSERT INTO imagenes (nombre_archivo, nombre_display, ruta_completa, extension, tamaño_kb, tipo_imagen, descripcion, activa) VALUES (?, ?, ?, ?, ?, 'avatar', ?, 1)");
        $tamaño_kb = round($archivo['size'] / 1024);
        $nombre_display = 'Avatar personalizado - Usuario ' . $usuario_id;
        $descripcion = 'Avatar personalizado subido por el usuario';
        
        $stmt->execute([
            $nombre_archivo,
            $nombre_display,
            $ruta_destino,
            $extension,
            $tamaño_kb,
            $descripcion
        ]);
        
        // Actualizar el avatar del usuario
        $stmt = $pdo->prepare("UPDATE usuarios SET avatar = ? WHERE id = ?");
        $stmt->execute([$nombre_archivo, $usuario_id]);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Avatar personalizado subido y asignado correctamente',
            'avatar' => $nombre_archivo
        ]);
        
    } else {
        throw new Exception('Tipo de operación no válido');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
}
?>
