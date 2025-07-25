<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

// Incluir conexión a la base de datos
require_once '../bd/conexion.php';

// Obtener información del usuario
$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['usuario_nombre'] ?? 'Usuario';

try {
    // Consultar datos del usuario
    $stmt = $pdo->prepare("
        SELECT 
            u.usuario,
            u.email,
            u.monedas_totales,
            u.partidas_ganadas,
            u.partidas_fallidas,
            c.nombre_categoria as nivel_actual,
            u.fecha_registro
        FROM usuarios u
        LEFT JOIN categoria c ON u.cod_categoria = c.id_categoria
        WHERE u.id = ?
    ");
    $stmt->execute([$usuario_id]);
    $datosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Consultar historial de monedas
    $stmtHistorial = $pdo->prepare("
        SELECT 
            fecha,
            monedas_ganadas,
            categoria,
            porcentaje_aciertos,
            tiempo_total,
            partida_ganada
        FROM historial_monedas 
        WHERE usuario_id = ? 
        ORDER BY fecha DESC
    ");
    $stmtHistorial->execute([$usuario_id]);
    $historial = $stmtHistorial->fetchAll(PDO::FETCH_ASSOC);

    // Configurar headers para descarga de Excel
    $filename = "ReichMind_Estadisticas_" . $usuario_nombre . "_" . date('Y-m-d_H-i-s') . ".csv";
    
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');

    // Crear el contenido del CSV con BOM para UTF-8
    echo "\xEF\xBB\xBF"; // BOM para UTF-8

    // Información del usuario
    echo "REICHMIND - ESTADÍSTICAS DE TRIVIA\n";
    echo "=================================\n\n";
    
    echo "DATOS DEL USUARIO\n";
    echo "Nombre de Usuario," . ($datosUsuario['usuario'] ?? 'N/A') . "\n";
    echo "Email," . ($datosUsuario['email'] ?? 'N/A') . "\n";
    echo "Nivel Actual," . ($datosUsuario['nivel_actual'] ?? 'Sin asignar') . "\n";
    echo "Monedas Totales," . ($datosUsuario['monedas_totales'] ?? 0) . "\n";
    echo "Partidas Ganadas," . ($datosUsuario['partidas_ganadas'] ?? 0) . "\n";
    echo "Partidas Fallidas," . ($datosUsuario['partidas_fallidas'] ?? 0) . "\n";
    echo "Fecha de Registro," . ($datosUsuario['fecha_registro'] ?? 'N/A') . "\n";
    echo "Fecha de Reporte," . date('d/m/Y H:i:s') . "\n\n";

    // Estadísticas calculadas
    $totalPartidas = ($datosUsuario['partidas_ganadas'] ?? 0) + ($datosUsuario['partidas_fallidas'] ?? 0);
    $porcentajeExito = $totalPartidas > 0 ? round((($datosUsuario['partidas_ganadas'] ?? 0) / $totalPartidas) * 100, 2) : 0;
    
    echo "ESTADÍSTICAS CALCULADAS\n";
    echo "Total de Partidas Jugadas," . $totalPartidas . "\n";
    echo "Porcentaje de Éxito," . $porcentajeExito . "%\n";
    echo "Promedio de Monedas por Partida," . ($totalPartidas > 0 ? round(($datosUsuario['monedas_totales'] ?? 0) / $totalPartidas, 2) : 0) . "\n\n";

    // Historial de partidas
    echo "HISTORIAL DE PARTIDAS\n";
    echo "Fecha,Monedas Ganadas,Categoría,Porcentaje Aciertos,Tiempo Total,Partida Ganada\n";
    
    foreach ($historial as $partida) {
        $fecha = date('d/m/Y H:i:s', strtotime($partida['fecha']));
        $monedas = $partida['monedas_ganadas'] ?? 0;
        $categoria = $partida['categoria'] ?? 'N/A';
        $porcentaje = $partida['porcentaje_aciertos'] ?? 0;
        $tiempo = $partida['tiempo_total'] ?? 0;
        $ganada = isset($partida['partida_ganada']) && $partida['partida_ganada'] ? 'Sí' : 'No';
        
        // Formatear tiempo
        $minutos = floor($tiempo / 60);
        $segundos = $tiempo % 60;
        $tiempoFormateado = sprintf("%d:%02d", $minutos, $segundos);
        
        echo "$fecha,$monedas,$categoria,$porcentaje%,$tiempoFormateado,$ganada\n";
    }

    // Análisis por categoría
    echo "\nANÁLISIS POR CATEGORÍA\n";
    echo "Categoría,Partidas Jugadas,Monedas Totales,Promedio Porcentaje,Promedio Tiempo\n";
    
    $stmtAnalisis = $pdo->prepare("
        SELECT 
            categoria,
            COUNT(*) as total_partidas,
            SUM(monedas_ganadas) as total_monedas,
            AVG(porcentaje_aciertos) as promedio_porcentaje,
            AVG(tiempo_total) as promedio_tiempo
        FROM historial_monedas 
        WHERE usuario_id = ? 
        GROUP BY categoria
        ORDER BY categoria
    ");
    $stmtAnalisis->execute([$usuario_id]);
    $analisisCategoria = $stmtAnalisis->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($analisisCategoria as $categoria) {
        $nombre = $categoria['categoria'] ?? 'N/A';
        $partidas = $categoria['total_partidas'] ?? 0;
        $monedas = $categoria['total_monedas'] ?? 0;
        $promedioPorcentaje = round($categoria['promedio_porcentaje'] ?? 0, 2);
        
        $promedioTiempo = $categoria['promedio_tiempo'] ?? 0;
        $minutos = floor($promedioTiempo / 60);
        $segundos = round($promedioTiempo % 60);
        $tiempoFormateado = sprintf("%d:%02d", $minutos, $segundos);
        
        echo "$nombre,$partidas,$monedas,$promedioPorcentaje%,$tiempoFormateado\n";
    }

    // Resumen final
    echo "\nRESUMEN FINAL\n";
    echo "=============\n";
    echo "Este reporte fue generado automáticamente por ReichMind\n";
    echo "Aplicación de trivia desarrollada para el aprendizaje interactivo\n";
    echo "Fecha de generación: " . date('d/m/Y H:i:s') . "\n";
    echo "Usuario: " . $usuario_nombre . "\n";
    echo "\n¡Sigue practicando para mejorar tus estadísticas! 🧠🎯\n";

} catch (PDOException $e) {
    // En caso de error, mostrar mensaje de error
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Error al generar las estadísticas: " . $e->getMessage();
    exit();
}
?>
