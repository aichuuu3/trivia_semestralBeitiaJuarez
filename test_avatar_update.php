<?php
require_once 'bd/claseBD.php';
$db = new DB();

echo "Testing avatar update method...\n";

// Test 1: Verificar estado actual
$admin = $db->obtenerAdministrador(1);
echo "Avatar actual: " . ($admin['avatar'] ?? 'NULL') . "\n";

// Test 2: Intentar actualizar avatar
$result = $db->actualizarAvatarAdministrador(1, 'Skirk.jpg');
echo "Resultado de actualización: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";

// Test 3: Verificar cambio
$admin = $db->obtenerAdministrador(1);
echo "Nuevo avatar: " . ($admin['avatar'] ?? 'NULL') . "\n";

// Test 4: Volver al original
$result = $db->actualizarAvatarAdministrador(1, 'avatar.png');
echo "Resultado volver al original: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";

$admin = $db->obtenerAdministrador(1);
echo "Avatar final: " . ($admin['avatar'] ?? 'NULL') . "\n";
?>
