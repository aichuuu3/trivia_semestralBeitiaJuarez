<?php
session_start();
require_once '../bd/claseBD.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$db = new DB();
$pdo = $db->getPdo();

// Obtener datos del usuario
$stmt = $pdo->prepare("SELECT nombre, email, cod_categoria, monedas_totales FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    http_response_code(404);
    echo json_encode(['error' => 'Usuario no encontrado']);
    exit;
}

// Obtener nombre de la categoría
$stmt = $pdo->prepare("SELECT nombre_categoria FROM categoria WHERE id_categoria = ?");
$stmt->execute([$usuario['cod_categoria']]);
$categoria = $stmt->fetch(PDO::FETCH_ASSOC);
$nivel_usuario = $categoria ? $categoria['nombre_categoria'] : 'No definido';

// Obtener historial de monedas (si existe)
$historial_monedas = [];
try {
    $stmt = $pdo->prepare("SELECT fecha, monedas_ganadas, categoria, tema, puntos, tiempo_total 
                          FROM historial_monedas 
                          WHERE usuario_id = ? 
                          ORDER BY fecha DESC 
                          LIMIT 50");
    $stmt->execute([$usuario_id]);
    $historial_monedas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si la tabla no existe, continuar sin historial
    $historial_monedas = [];
}

// Función para limpiar datos para CSV
function limpiarCsv($data) {
    return '"' . str_replace('"', '""', $data) . '"';
}

// Generar archivo CSV (más compatible que Excel XML)
$filename = 'estadisticas_trivia_' . date('Y-m-d_H-i-s') . '.csv';

// Configurar headers para descarga de CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Crear output buffer
$output = fopen('php://output', 'w');

// BOM para UTF-8 (ayuda con caracteres especiales en Excel)
fwrite($output, "\xEF\xBB\xBF");

// Información del usuario
fputcsv($output, ['ESTADÍSTICAS DE TRIVIA - REICHMIND'], ',');
fputcsv($output, [], ','); // Línea vacía
fputcsv($output, ['INFORMACIÓN DEL USUARIO'], ',');
fputcsv($output, ['Campo', 'Valor'], ',');
fputcsv($output, ['Nombre', $usuario['nombre']], ',');
fputcsv($output, ['Email', $usuario['email']], ',');
fputcsv($output, ['Nivel', $nivel_usuario], ',');
fputcsv($output, ['Monedas Totales', $usuario['monedas_totales']], ',');
fputcsv($output, ['Fecha del Reporte', date('Y-m-d H:i:s')], ',');
fputcsv($output, [], ','); // Línea vacía

// Estadísticas resumidas
$total_partidas = count($historial_monedas);
$total_monedas_ganadas = array_sum(array_column($historial_monedas, 'monedas_ganadas'));
$total_puntos = array_sum(array_column($historial_monedas, 'puntos'));

fputcsv($output, ['RESUMEN DE ESTADÍSTICAS'], ',');
fputcsv($output, ['Métrica', 'Valor'], ',');
fputcsv($output, ['Total de Partidas Jugadas', $total_partidas], ',');
fputcsv($output, ['Total de Monedas Ganadas en Partidas', $total_monedas_ganadas], ',');
fputcsv($output, ['Total de Puntos Acumulados', $total_puntos], ',');

if ($total_partidas > 0) {
    $promedio_monedas = round($total_monedas_ganadas / $total_partidas, 2);
    $promedio_puntos = round($total_puntos / $total_partidas, 2);
    fputcsv($output, ['Promedio de Monedas por Partida', $promedio_monedas], ',');
    fputcsv($output, ['Promedio de Puntos por Partida', $promedio_puntos], ',');
}

fputcsv($output, [], ','); // Línea vacía

// Historial de partidas
if (!empty($historial_monedas)) {
    fputcsv($output, ['HISTORIAL DE PARTIDAS'], ',');
    fputcsv($output, [
        'Fecha', 
        'Monedas Ganadas', 
        'Categoría', 
        'Tema', 
        'Puntos', 
        'Tiempo (segundos)'
    ], ',');
    
    foreach ($historial_monedas as $registro) {
        fputcsv($output, [
            $registro['fecha'] ?? 'N/A',
            $registro['monedas_ganadas'] ?? 0,
            $registro['categoria'] ?? 'N/A',
            $registro['tema'] ?? 'N/A',
            $registro['puntos'] ?? 0,
            $registro['tiempo_total'] ?? 0
        ], ',');
    }
} else {
    fputcsv($output, ['HISTORIAL DE PARTIDAS'], ',');
    fputcsv($output, ['Estado', 'No hay partidas registradas'], ',');
    fputcsv($output, ['Recomendación', 'Juega algunas trivias para generar estadísticas'], ',');
}

fputcsv($output, [], ','); // Línea vacía
fputcsv($output, ['Generado automáticamente por ReichMind', date('Y-m-d H:i:s')], ',');

fclose($output);
exit;
?>
