<?php
// Script de diagnóstico del sistema de monedas
session_start();
require_once '../bd/conexion.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico - Sistema de Monedas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #533483; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        h2 { color: #533483; border-bottom: 3px solid #533483; padding-bottom: 10px; }
        h3 { color: #333; margin-top: 25px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        .info { background: #e7f3ff; padding: 10px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔍 Diagnóstico del Sistema de Monedas</h2>
        
        <?php
        try {
            echo "<h3>✅ Conexión a Base de Datos</h3>";
            echo "<p class='success'>Conexión exitosa</p>";
            
            // Verificar estructura de tabla usuarios
            echo "<h3>📋 Estructura de Tabla 'usuarios'</h3>";
            $stmt = $pdo->query("DESCRIBE usuarios");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<table>";
            echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            $hasMonedas = false;
            foreach ($columns as $column) {
                if ($column['Field'] === 'monedas_totales') $hasMonedas = true;
                echo "<tr>";
                echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($column['Default'] ?? '') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            if ($hasMonedas) {
                echo "<p class='success'>✅ Columna 'monedas_totales' encontrada</p>";
            } else {
                echo "<p class='error'>❌ Columna 'monedas_totales' NO encontrada</p>";
            }
            
            // Verificar tabla historial_monedas
            echo "<h3>📊 Tabla 'historial_monedas'</h3>";
            try {
                $stmt = $pdo->query("DESCRIBE historial_monedas");
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<p class='success'>✅ Tabla existe</p>";
                echo "<table>";
                echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
                foreach ($columns as $column) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
                    echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
                    echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
                    echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
                    echo "<td>" . htmlspecialchars($column['Default'] ?? '') . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } catch (PDOException $e) {
                echo "<p class='error'>❌ Tabla no existe: " . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<div class='info'><strong>Solución:</strong> Ejecuta el archivo historial_monedas.sql en phpMyAdmin</div>";
            }
            
            // Verificar datos de usuarios
            echo "<h3>👥 Usuarios en la Base de Datos</h3>";
            $stmt = $pdo->query("SELECT id, nombre, email, monedas_totales, cod_categoria FROM usuarios LIMIT 5");
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<table>";
            echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Monedas</th><th>Categoría</th></tr>";
            foreach ($usuarios as $usuario) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($usuario['id']) . "</td>";
                echo "<td>" . htmlspecialchars($usuario['nombre']) . "</td>";
                echo "<td>" . htmlspecialchars($usuario['email']) . "</td>";
                echo "<td>" . htmlspecialchars($usuario['monedas_totales']) . "</td>";
                echo "<td>" . htmlspecialchars($usuario['cod_categoria'] ?? 'Sin asignar') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Verificar sesión actual
            echo "<h3>🔐 Información de Sesión</h3>";
            if (isset($_SESSION['usuario_id'])) {
                echo "<p class='success'>✅ Sesión activa</p>";
                echo "<ul>";
                echo "<li><strong>Usuario ID:</strong> " . htmlspecialchars($_SESSION['usuario_id']) . "</li>";
                echo "<li><strong>Usuario Nombre:</strong> " . htmlspecialchars($_SESSION['usuario_nombre'] ?? 'No establecido') . "</li>";
                echo "<li><strong>Usuario Monedas:</strong> " . htmlspecialchars($_SESSION['usuario_monedas'] ?? 'No establecido') . "</li>";
                echo "</ul>";
            } else {
                echo "<p class='warning'>⚠️ No hay sesión activa</p>";
                echo "<div class='info'>Para probar el sistema, inicia sesión primero</div>";
            }
            
            // Test de actualización de monedas
            echo "<h3>🧪 Test de Sistema de Monedas</h3>";
            if (isset($_SESSION['usuario_id'])) {
                echo "<div class='info'>";
                echo "<p><strong>Test automático disponible:</strong></p>";
                echo "<p>El sistema está configurado correctamente. Puedes probar:</p>";
                echo "<ol>";
                echo "<li>Jugar una trivia completa</li>";
                echo "<li>Verificar que las monedas se actualicen en tiempo real</li>";
                echo "<li>Comprobar el historial en la base de datos</li>";
                echo "</ol>";
                echo "</div>";
            } else {
                echo "<p class='warning'>⚠️ Inicia sesión para realizar pruebas</p>";
            }
            
        } catch (PDOException $e) {
            echo "<h3 class='error'>❌ Error de Base de Datos</h3>";
            echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
        
        <h3>🔧 Archivos del Sistema</h3>
        <ul>
            <li><strong>actualizarMonedas.php:</strong> <?php echo file_exists('actualizarMonedas.php') ? '<span class="success">✅ Existe</span>' : '<span class="error">❌ No existe</span>'; ?></li>
            <li><strong>historial_monedas.sql:</strong> <?php echo file_exists('../bd/historial_monedas.sql') ? '<span class="success">✅ Existe</span>' : '<span class="error">❌ No existe</span>'; ?></li>
            <li><strong>conexion.php:</strong> <?php echo file_exists('../bd/conexion.php') ? '<span class="success">✅ Existe</span>' : '<span class="error">❌ No existe</span>'; ?></li>
        </ul>
    </div>
</body>
</html>
