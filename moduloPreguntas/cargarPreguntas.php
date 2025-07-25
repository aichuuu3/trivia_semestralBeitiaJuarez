<?php
try {
    // Incluir conexión a la base de datos
    require_once '../bd/conexion.php';
    
    // Consulta para obtener preguntas con sus respuestas y temas
    $query = "SELECT p.id_pregunta, p.texto_pregunta, p.puntos, p.activa, 
             c.nombre_categoria, t.nombre_tema,
             GROUP_CONCAT(CASE WHEN r.es_correcta = 1 THEN r.texto_respuesta END) as respuesta_correcta,
             GROUP_CONCAT(CASE WHEN r.es_correcta = 0 THEN r.texto_respuesta END SEPARATOR ' | ') as respuestas_incorrectas
             FROM preguntas p 
             LEFT JOIN categoria c ON p.cod_categoria = c.id_categoria
             LEFT JOIN temas t ON p.id_tema = t.id_tema
             LEFT JOIN respuestas r ON p.id_pregunta = r.id_pregunta
             GROUP BY p.id_pregunta, p.texto_pregunta, p.puntos, p.activa, c.nombre_categoria, t.nombre_tema
             ORDER BY p.id_pregunta DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($preguntas) > 0) {
        foreach ($preguntas as $pregunta) {
            echo "<tr>";
            echo "<td>" . $pregunta['id_pregunta'] . "</td>";
            
            // Pregunta con límite de caracteres
            $texto_pregunta = strlen($pregunta['texto_pregunta']) > 100 
                            ? substr($pregunta['texto_pregunta'], 0, 100) . '...' 
                            : $pregunta['texto_pregunta'];
            echo "<td>" . htmlspecialchars($texto_pregunta) . "</td>";
            
            // Categoría con badge
            $categoria_class = '';
            switch ($pregunta['nombre_categoria']) {
                case 'Principiante':
                    $categoria_class = 'badge-success';
                    break;
                case 'Novato':
                    $categoria_class = 'badge-warning';
                    break;
                case 'Experto':
                    $categoria_class = 'badge-danger';
                    break;
                default:
                    $categoria_class = 'badge-secondary';
            }
            echo "<td><span class='badge {$categoria_class}'>" . htmlspecialchars($pregunta['nombre_categoria'] ?? 'Sin categoría') . "</span></td>";
            
            // Tema con badge
            $tema_class = '';
            switch ($pregunta['nombre_tema']) {
                case 'CRUD':
                    $tema_class = 'badge-primary';
                    break;
                case 'Laravel':
                    $tema_class = 'badge-info';
                    break;
                case 'PHP':
                    $tema_class = 'badge-dark';
                    break;
                default:
                    $tema_class = 'badge-light';
            }
            echo "<td><span class='badge {$tema_class}'>" . htmlspecialchars($pregunta['nombre_tema'] ?? 'Sin tema') . "</span></td>";
            
            // Respuesta correcta
            echo "<td>" . htmlspecialchars($pregunta['respuesta_correcta'] ?? 'N/A') . "</td>";
            
            // Respuestas incorrectas
            $respuestas_incorrectas = $pregunta['respuestas_incorrectas'] ?? 'N/A';
            if (strlen($respuestas_incorrectas) > 80) {
                $respuestas_incorrectas = substr($respuestas_incorrectas, 0, 80) . '...';
            }
            echo "<td>" . htmlspecialchars($respuestas_incorrectas) . "</td>";
            
            // Estado
            $estado_class = $pregunta['activa'] ? 'badge-success' : 'badge-secondary';
            $estado_texto = $pregunta['activa'] ? 'Activa' : 'Inactiva';
            echo "<td><span class='badge {$estado_class}'>{$estado_texto}</span></td>";
            
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7' class='text-center'>No hay preguntas registradas</td></tr>";
    }

} catch (Exception $e) {
    echo "<tr><td colspan='7' class='text-center text-danger'>Error al cargar preguntas: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>
