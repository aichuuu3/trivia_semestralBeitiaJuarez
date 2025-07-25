<?php
try {
    // Incluir conexión a la base de datos
    require_once '../bd/conexion.php';
    
    // Consulta para obtener todas las preguntas activas
    $query = "SELECT p.id_pregunta, p.texto_pregunta, c.nombre_categoria, t.nombre_tema
              FROM preguntas p 
              LEFT JOIN categoria c ON p.cod_categoria = c.id_categoria
              LEFT JOIN temas t ON p.id_tema = t.id_tema
              WHERE p.activa = 1
              ORDER BY p.id_pregunta DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Generar opciones HTML
    echo '<option value="">Selecciona una pregunta</option>';
    
    foreach ($preguntas as $pregunta) {
        $texto_corto = strlen($pregunta['texto_pregunta']) > 50 
                      ? substr($pregunta['texto_pregunta'], 0, 50) . '...' 
                      : $pregunta['texto_pregunta'];
        
        $categoria = $pregunta['nombre_categoria'] ?? 'Sin categoría';
        $tema = $pregunta['nombre_tema'] ?? 'Sin tema';
        
        echo '<option value="' . $pregunta['id_pregunta'] . '">';
        echo 'ID: ' . $pregunta['id_pregunta'] . ' - ' . htmlspecialchars($texto_corto);
        echo ' (' . htmlspecialchars($categoria) . ' - ' . htmlspecialchars($tema) . ')';
        echo '</option>';
    }

} catch (Exception $e) {
    echo '<option value="">Error al cargar preguntas</option>';
}
?>
