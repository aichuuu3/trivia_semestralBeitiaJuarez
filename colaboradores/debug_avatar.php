<?php
session_start();
require_once '../bd/claseBD.php';

if (!isset($_SESSION['colaborador_id'])) {
    die('No autorizado');
}

$colaborador_id = $_SESSION['colaborador_id'];
$db = new DB();

echo "<h2>🔍 Debug Avatar Colaborador</h2>";
echo "<hr>";

// 1. Verificar datos actuales del colaborador
$colaborador = $db->obtenerColaborador($colaborador_id);
echo "<h3>📊 Datos actuales del colaborador:</h3>";
echo "<pre>";
print_r($colaborador);
echo "</pre>";

// 2. Verificar si se está enviando formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>📤 Datos POST recibidos:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h3>📁 Archivos recibidos:</h3>";
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
    
    // 3. Probar actualización de avatar con avatar predefinido
    if (isset($_POST['avatar']) && !empty($_POST['avatar'])) {
        $avatar_seleccionado = $_POST['avatar'];
        echo "<h3>🎭 Probando actualización con avatar predefinido: $avatar_seleccionado</h3>";
        
        $resultado = $db->actualizarAvatarColaborador($colaborador_id, $avatar_seleccionado);
        echo "Resultado de actualizarAvatarColaborador: " . ($resultado ? "✅ Éxito" : "❌ Error") . "<br>";
        
        // Verificar cambio en BD
        $colaborador_actualizado = $db->obtenerColaborador($colaborador_id);
        echo "Avatar en BD después de actualización: " . $colaborador_actualizado['avatar'] . "<br>";
    }
    
    // 4. Manejar subida de archivo
    if (isset($_FILES['nueva_imagen']) && $_FILES['nueva_imagen']['error'] === UPLOAD_ERR_OK) {
        echo "<h3>📸 Procesando nueva imagen...</h3>";
        
        $archivo = $_FILES['nueva_imagen'];
        $nombre_original = $archivo['name'];
        $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
        $tamaño_bytes = $archivo['size'];
        $tamaño_kb = round($tamaño_bytes / 1024);
        
        echo "Nombre original: $nombre_original<br>";
        echo "Extensión: $extension<br>";
        echo "Tamaño: $tamaño_kb KB<br>";
        
        // Validar tipo de archivo
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $extensiones_permitidas)) {
            echo "❌ Error: Extensión no permitida<br>";
        } else if ($tamaño_bytes > 5 * 1024 * 1024) {
            echo "❌ Error: Archivo demasiado grande<br>";
        } else {
            echo "✅ Validaciones pasadas<br>";
            
            // Generar nombre único
            $nombre_archivo = 'avatar_colaborador_' . $colaborador_id . '_' . time() . '.' . $extension;
            $ruta_destino = '../img/' . $nombre_archivo;
            $ruta_completa = '../img/' . $nombre_archivo;
            
            echo "Nombre de archivo: $nombre_archivo<br>";
            echo "Ruta destino: $ruta_destino<br>";
            
            // Intentar mover archivo
            if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
                echo "✅ Archivo movido exitosamente<br>";
                
                // Verificar que el archivo existe
                if (file_exists($ruta_destino)) {
                    echo "✅ Archivo confirmado en destino<br>";
                    echo "Tamaño final: " . filesize($ruta_destino) . " bytes<br>";
                } else {
                    echo "❌ Archivo no encontrado en destino<br>";
                }
                
                // Insertar en base de datos
                $nombre_display = 'Avatar Colaborador Debug';
                $insercion_resultado = $db->insertarImagen($nombre_archivo, $nombre_display, $ruta_completa, $extension, $tamaño_kb);
                echo "Resultado insertarImagen: " . ($insercion_resultado ? "✅ Éxito" : "❌ Error") . "<br>";
                
                if ($insercion_resultado) {
                    // Actualizar avatar del colaborador
                    $actualizacion_resultado = $db->actualizarAvatarColaborador($colaborador_id, $nombre_archivo);
                    echo "Resultado actualizarAvatarColaborador: " . ($actualizacion_resultado ? "✅ Éxito" : "❌ Error") . "<br>";
                    
                    // Verificar cambio en BD
                    $colaborador_final = $db->obtenerColaborador($colaborador_id);
                    echo "Avatar final en BD: " . $colaborador_final['avatar'] . "<br>";
                } else {
                    echo "❌ Error en inserción, eliminando archivo<br>";
                    unlink($ruta_destino);
                }
            } else {
                echo "❌ Error al mover archivo<br>";
                echo "Error info: " . error_get_last()['message'] . "<br>";
            }
        }
    } else if (isset($_FILES['nueva_imagen'])) {
        echo "<h3>❌ Error en archivo subido:</h3>";
        echo "Error code: " . $_FILES['nueva_imagen']['error'] . "<br>";
        $error_messages = [
            UPLOAD_ERR_OK => 'No hay error',
            UPLOAD_ERR_INI_SIZE => 'El archivo es demasiado grande (php.ini)',
            UPLOAD_ERR_FORM_SIZE => 'El archivo es demasiado grande (formulario)',
            UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
            UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta carpeta temporal',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir archivo',
            UPLOAD_ERR_EXTENSION => 'Extensión de PHP detuvo la subida'
        ];
        echo "Mensaje: " . ($error_messages[$_FILES['nueva_imagen']['error']] ?? 'Error desconocido') . "<br>";
    }
}

// 5. Listar avatares disponibles
echo "<h3>🎭 Avatares disponibles en BD:</h3>";
$pdo = $db->getPdo();
$stmt = $pdo->query("SELECT * FROM imagenes WHERE tipo_imagen = 'avatar' AND activa = 1");
$avatares = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($avatares);
echo "</pre>";

// 6. Verificar permisos de directorio
echo "<h3>📁 Información del directorio img:</h3>";
$img_dir = '../img/';
echo "Directorio existe: " . (is_dir($img_dir) ? "✅ Sí" : "❌ No") . "<br>";
echo "Es escribible: " . (is_writable($img_dir) ? "✅ Sí" : "❌ No") . "<br>";
echo "Ruta absoluta: " . realpath($img_dir) . "<br>";

?>

<hr>
<h3>🧪 Formulario de Prueba</h3>
<form method="post" enctype="multipart/form-data">
    <h4>Seleccionar Avatar Predefinido:</h4>
    <?php foreach($avatares as $avatar): ?>
        <label>
            <input type="radio" name="avatar" value="<?php echo $avatar['nombre_archivo']; ?>">
            <img src="../img/<?php echo $avatar['nombre_archivo']; ?>" width="50" height="50" style="border-radius: 50%;">
            <?php echo $avatar['nombre_display']; ?>
        </label><br>
    <?php endforeach; ?>
    
    <h4>O Subir Nueva Imagen:</h4>
    <input type="file" name="nueva_imagen" accept="image/*"><br><br>
    
    <button type="submit">Probar Actualización</button>
</form>
