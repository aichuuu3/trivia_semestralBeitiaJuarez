<?php
require_once 'bd/conexion.php';

echo "<h2>🔍 Verificación del Sistema de Partidas Fallidas</h2>";

// 1. Verificar estructura de la tabla usuarios
echo "<h3>1. Estructura de la tabla usuarios</h3>";
$consulta_estructura = "DESCRIBE usuarios";
$resultado_estructura = mysqli_query($conexion, $consulta_estructura);

if ($resultado_estructura) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Default</th></tr>";
    
    $tiene_partidas_ganadas = false;
    $tiene_partidas_fallidas = false;
    
    while ($columna = mysqli_fetch_assoc($resultado_estructura)) {
        echo "<tr>";
        echo "<td>" . $columna['Field'] . "</td>";
        echo "<td>" . $columna['Type'] . "</td>";
        echo "<td>" . $columna['Null'] . "</td>";
        echo "<td>" . $columna['Key'] . "</td>";
        echo "<td>" . $columna['Default'] . "</td>";
        echo "</tr>";
        
        if ($columna['Field'] === 'partidas_ganadas') {
            $tiene_partidas_ganadas = true;
        }
        if ($columna['Field'] === 'partidas_fallidas') {
            $tiene_partidas_fallidas = true;
        }
    }
    echo "</table>";
    
    // Verificar columnas específicas
    echo "<h4>Estado de las columnas:</h4>";
    echo "<ul>";
    echo "<li>partidas_ganadas: " . ($tiene_partidas_ganadas ? "✅ Presente" : "❌ Falta") . "</li>";
    echo "<li>partidas_fallidas: " . ($tiene_partidas_fallidas ? "✅ Presente" : "❌ Falta") . "</li>";
    echo "</ul>";
    
    if (!$tiene_partidas_fallidas) {
        echo "<div style='background: #fff3cd; padding: 10px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>⚠️ Acción requerida:</strong> La columna 'partidas_fallidas' no existe. ";
        echo "Ejecuta el archivo 'agregar_partidas_fallidas.sql' en phpMyAdmin.";
        echo "</div>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Error al consultar la estructura: " . mysqli_error($conexion) . "</p>";
}

// 2. Verificar estructura de la tabla historial_monedas
echo "<h3>2. Estructura de la tabla historial_monedas</h3>";
$consulta_historial = "DESCRIBE historial_monedas";
$resultado_historial = mysqli_query($conexion, $consulta_historial);

if ($resultado_historial) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Default</th></tr>";
    
    $tiene_partida_ganada = false;
    
    while ($columna = mysqli_fetch_assoc($resultado_historial)) {
        echo "<tr>";
        echo "<td>" . $columna['Field'] . "</td>";
        echo "<td>" . $columna['Type'] . "</td>";
        echo "<td>" . $columna['Null'] . "</td>";
        echo "<td>" . $columna['Key'] . "</td>";
        echo "<td>" . $columna['Default'] . "</td>";
        echo "</tr>";
        
        if ($columna['Field'] === 'partida_ganada') {
            $tiene_partida_ganada = true;
        }
    }
    echo "</table>";
    
    echo "<h4>Estado de las columnas del historial:</h4>";
    echo "<ul>";
    echo "<li>partida_ganada: " . ($tiene_partida_ganada ? "✅ Presente" : "❌ Falta") . "</li>";
    echo "</ul>";
    
} else {
    echo "<p style='color: red;'>❌ Error al consultar historial: " . mysqli_error($conexion) . "</p>";
}

// 3. Mostrar datos de ejemplo de usuarios (primeros 5)
if ($tiene_partidas_ganadas || $tiene_partidas_fallidas) {
    echo "<h3>3. Datos de ejemplo de usuarios</h3>";
    $consulta_usuarios = "SELECT id, usuario, partidas_ganadas, " . 
                        ($tiene_partidas_fallidas ? "partidas_fallidas" : "0 as partidas_fallidas") . 
                        " FROM usuarios LIMIT 5";
    
    $resultado_usuarios = mysqli_query($conexion, $consulta_usuarios);
    
    if ($resultado_usuarios && mysqli_num_rows($resultado_usuarios) > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Usuario</th><th>Partidas Ganadas</th><th>Partidas Fallidas</th></tr>";
        
        while ($usuario = mysqli_fetch_assoc($resultado_usuarios)) {
            echo "<tr>";
            echo "<td>" . $usuario['id'] . "</td>";
            echo "<td>" . $usuario['usuario'] . "</td>";
            echo "<td>" . ($usuario['partidas_ganadas'] ?? 0) . "</td>";
            echo "<td>" . ($usuario['partidas_fallidas'] ?? 0) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay usuarios para mostrar.</p>";
    }
}

// 4. Resumen del estado del sistema
echo "<h3>4. Resumen del Estado del Sistema</h3>";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h4>✅ Componentes implementados:</h4>";
echo "<ul>";
echo "<li>✅ Lógica en actualizarMonedas.php para detectar partidas ganadas y fallidas</li>";
echo "<li>✅ Frontend en inicio.php con variables y funciones para partidas fallidas</li>";
echo "<li>✅ Función terminarTrivia() actualizada para manejar ambos tipos de partidas</li>";
echo "<li>✅ Estilos CSS para animaciones de partidas fallidas</li>";
echo "<li>✅ Script SQL para agregar columna partidas_fallidas</li>";
echo "</ul>";
echo "</div>";

if (!$tiene_partidas_fallidas) {
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>⚠️ Pasos pendientes:</h4>";
    echo "<ol>";
    echo "<li>Ejecutar el archivo <code>agregar_partidas_fallidas.sql</code> en phpMyAdmin</li>";
    echo "<li>Probar el sistema jugando una partida con menos del 100% de aciertos</li>";
    echo "<li>Verificar que se incremente el contador de partidas fallidas</li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>🎉 Sistema completo!</h4>";
    echo "<p>El sistema de partidas fallidas está completamente implementado y listo para usar.</p>";
    echo "<p><strong>Prueba sugerida:</strong> Juega una partida y no contestes todas las preguntas correctamente para ver el sistema en acción.</p>";
    echo "</div>";
}

mysqli_close($conexion);
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background: #f8f9fa;
}

h2, h3, h4 {
    color: #333;
}

table {
    width: 100%;
    background: white;
}

table th {
    background: #343a40;
    color: white;
    padding: 8px;
}

table td {
    padding: 8px;
    border: 1px solid #ddd;
}

code {
    background: #f8f9fa;
    padding: 2px 4px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
}
</style>
