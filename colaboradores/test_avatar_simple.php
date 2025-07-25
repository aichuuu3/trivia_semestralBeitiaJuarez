<?php
session_start();
require_once '../bd/claseBD.php';

if (!isset($_SESSION['colaborador_id'])) {
    die('No autorizado');
}

$colaborador_id = $_SESSION['colaborador_id'];
$db = new DB();

echo "<h2>🧪 Test Simple de Avatar</h2>";

// Obtener datos actuales
$colaborador_antes = $db->obtenerColaborador($colaborador_id);
echo "<h3>📊 Antes de la actualización:</h3>";
echo "Avatar actual: " . $colaborador_antes['avatar'] . "<br>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_avatar'])) {
    $nuevo_avatar = $_POST['test_avatar'];
    echo "<h3>🔄 Actualizando a: $nuevo_avatar</h3>";
    
    // Probar la actualización
    $resultado = $db->actualizarAvatarColaborador($colaborador_id, $nuevo_avatar);
    echo "Resultado de actualizarAvatarColaborador: " . ($resultado ? "✅ TRUE" : "❌ FALSE") . "<br>";
    
    // Verificar en base de datos
    $colaborador_despues = $db->obtenerColaborador($colaborador_id);
    echo "Avatar después de actualización: " . $colaborador_despues['avatar'] . "<br>";
    
    if ($colaborador_despues['avatar'] === $nuevo_avatar) {
        echo "✅ ¡Avatar actualizado correctamente en BD!<br>";
    } else {
        echo "❌ Error: Avatar no se actualizó en BD<br>";
    }
}

// Verificar avatares disponibles
$pdo = $db->getPdo();
$stmt = $pdo->query("SELECT nombre_archivo, nombre_display FROM imagenes WHERE tipo_imagen = 'avatar' AND activa = 1");
$avatares_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h3>🎭 Test con Avatares Disponibles:</h3>
<form method="post">
    <?php foreach($avatares_disponibles as $avatar): ?>
        <label>
            <input type="radio" name="test_avatar" value="<?php echo $avatar['nombre_archivo']; ?>" 
                   <?php echo ($colaborador_antes['avatar'] === $avatar['nombre_archivo']) ? 'checked' : ''; ?>>
            <img src="../img/<?php echo $avatar['nombre_archivo']; ?>" width="50" height="50" style="border-radius: 50%;">
            <?php echo $avatar['nombre_display']; ?>
        </label><br>
    <?php endforeach; ?>
    <br>
    <button type="submit">Probar Actualización</button>
</form>

<hr>
<h3>🔍 Query Manual:</h3>
<?php
// Probar query manual
try {
    $stmt = $pdo->prepare("SELECT * FROM colaboradores WHERE id = ?");
    $stmt->execute([$colaborador_id]);
    $resultado_manual = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($resultado_manual);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error en query manual: " . $e->getMessage();
}
?>
