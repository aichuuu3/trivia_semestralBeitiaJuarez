<?php
require_once 'bd/claseBD.php';
$db = new DB();
$pdo = $db->getPdo();

echo "Verificando estructura de la tabla imagenes...\n";
$stmt = $pdo->query("DESCRIBE imagenes");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($columns as $column) {
    echo "- {$column['Field']} ({$column['Type']}) {$column['Null']} {$column['Default']}\n";
}
?>
