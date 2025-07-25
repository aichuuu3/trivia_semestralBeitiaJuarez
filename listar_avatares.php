<?php
require_once 'bd/claseBD.php';
$db = new DB();
$pdo = $db->getPdo();

echo "Avatares disponibles:\n";
$stmt = $pdo->query("SELECT nombre_archivo, nombre_display FROM imagenes WHERE tipo_imagen = 'avatar' AND activa = 1");
$avatares = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($avatares as $avatar) {
    echo "- " . $avatar['nombre_archivo'] . " (" . $avatar['nombre_display'] . ")\n";
}
?>
