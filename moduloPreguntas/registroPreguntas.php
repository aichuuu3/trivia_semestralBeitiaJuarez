<?php
require("../bd/conexion.php");
require("../bd/claseBD.php");

// Respuesta JSON estándar
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

if (!empty($_POST)) {
    //crea instancia de la clase DB
    $db = new DB();
    
    // determinar operación
    $operacion = $_POST['operation'] ?? $_POST['operacion'] ?? 'add';
    
    // switch para todas las operaciones
    switch ($operacion) {
        case 'delete':
        case 'eliminar':
            $id = $_POST['pregunta-id'] ?? $_POST['id'];
            
            if (empty($id)) {
                $response['message'] = 'ID de pregunta no especificado';
                echo json_encode($response);
                break;
            }
            
            try {
                global $pdo;
                $pdo->beginTransaction();
                
                // Eliminar respuestas asociadas
                $queryRespuestas = "DELETE FROM respuestas WHERE id_pregunta = :id";
                $stmtRespuestas = $pdo->prepare($queryRespuestas);
                $stmtRespuestas->bindParam(':id', $id, PDO::PARAM_INT);
                $stmtRespuestas->execute();
                
                // Eliminar pregunta
                $queryPregunta = "DELETE FROM preguntas WHERE id_pregunta = :id";
                $stmtPregunta = $pdo->prepare($queryPregunta);
                $stmtPregunta->bindParam(':id', $id, PDO::PARAM_INT);
                $resultado = $stmtPregunta->execute();
                
                if ($resultado && $stmtPregunta->rowCount() > 0) {
                    $pdo->commit();
                    $response['success'] = true;
                    $response['message'] = 'Pregunta eliminada correctamente';
                } else {
                    $pdo->rollBack();
                    $response['message'] = 'Pregunta no encontrada o ya fue eliminada';
                }
                
                echo json_encode($response);
            } catch (Exception $e) {
                $pdo->rollBack();
                $response['message'] = 'Error al eliminar la pregunta: ' . $e->getMessage();
                echo json_encode($response);
            }
            break;
            
        case 'edit':
        case 'actualizar':
            $id = $_POST['pregunta-id'] ?? $_POST['id'];
            
            if (empty($id)) {
                $response['message'] = 'ID de pregunta no especificado';
                echo json_encode($response);
                break;
            }
            
            // Validar datos requeridos
            $errores = [];
            
            if (empty($_POST['pregunta'])) {
                $errores[] = 'La pregunta es requerida';
            }
            if (empty($_POST['categoria'])) {
                $errores[] = 'La categoría es requerida';
            }
            if (empty($_POST['tema'])) {
                $errores[] = 'El tema es requerido';
            }
            if (empty($_POST['respuesta_correcta'])) {
                $errores[] = 'La respuesta correcta es requerida';
            }
            if (empty($_POST['respuesta_incorrecta_1'])) {
                $errores[] = 'Al menos una respuesta incorrecta es requerida';
            }
            
            if (!empty($errores)) {
                $response['errors'] = $errores;
                $response['message'] = 'Datos inválidos';
                echo json_encode($response);
                break;
            }
            
            // Limpiar y preparar datos
            $pregunta = trim($_POST['pregunta']);
            $categoria = (int)$_POST['categoria'];
            $tema = (int)$_POST['tema'];
            $puntos = 10; // Fijo en 10
            $respuesta_correcta = trim($_POST['respuesta_correcta']);
            $respuesta_incorrecta_1 = trim($_POST['respuesta_incorrecta_1']);
            $respuesta_incorrecta_2 = trim($_POST['respuesta_incorrecta_2'] ?? '');
            $respuesta_incorrecta_3 = trim($_POST['respuesta_incorrecta_3'] ?? '');
            
            try {
                global $pdo;
                $pdo->beginTransaction();
                
                // Actualizar pregunta
                $queryPregunta = "UPDATE preguntas SET 
                                 texto_pregunta = :pregunta,
                                 cod_categoria = :categoria,
                                 id_tema = :tema,
                                 puntos = :puntos
                                 WHERE id_pregunta = :id";
                $stmtPregunta = $pdo->prepare($queryPregunta);
                $stmtPregunta->bindParam(':pregunta', $pregunta);
                $stmtPregunta->bindParam(':categoria', $categoria);
                $stmtPregunta->bindParam(':tema', $tema);
                $stmtPregunta->bindParam(':puntos', $puntos);
                $stmtPregunta->bindParam(':id', $id, PDO::PARAM_INT);
                
                if (!$stmtPregunta->execute()) {
                    throw new Exception('Error al actualizar la pregunta');
                }
                
                // Eliminar respuestas existentes
                $queryDeleteRespuestas = "DELETE FROM respuestas WHERE id_pregunta = :id";
                $stmtDeleteRespuestas = $pdo->prepare($queryDeleteRespuestas);
                $stmtDeleteRespuestas->bindParam(':id', $id, PDO::PARAM_INT);
                $stmtDeleteRespuestas->execute();
                
                // Insertar nuevas respuestas
                $queryRespuesta = "INSERT INTO respuestas (id_pregunta, texto_respuesta, es_correcta) VALUES (:id_pregunta, :respuesta, :es_correcta)";
                $stmtRespuesta = $pdo->prepare($queryRespuesta);
                
                // Respuesta correcta
                $stmtRespuesta->execute([
                    ':id_pregunta' => $id,
                    ':respuesta' => $respuesta_correcta,
                    ':es_correcta' => 1
                ]);
                
                // Respuestas incorrectas
                $respuestas_incorrectas = [$respuesta_incorrecta_1];
                if (!empty($respuesta_incorrecta_2)) {
                    $respuestas_incorrectas[] = $respuesta_incorrecta_2;
                }
                if (!empty($respuesta_incorrecta_3)) {
                    $respuestas_incorrectas[] = $respuesta_incorrecta_3;
                }
                
                foreach ($respuestas_incorrectas as $respuesta_incorrecta) {
                    $stmtRespuesta->execute([
                        ':id_pregunta' => $id,
                        ':respuesta' => $respuesta_incorrecta,
                        ':es_correcta' => 0
                    ]);
                }
                
                // Confirmar transacción
                $pdo->commit();
                
                $response['success'] = true;
                $response['message'] = 'Pregunta actualizada correctamente';
                echo json_encode($response);
                
            } catch (Exception $e) {
                // Revertir transacción en caso de error
                $pdo->rollBack();
                $response['message'] = 'Error al actualizar la pregunta: ' . $e->getMessage();
                echo json_encode($response);
            }
            break;
            
        case 'obtener':
            $id = $_POST['id'];
            $pregunta_data = $db->obtenerPregunta($id);
            
            if ($pregunta_data) {
                echo json_encode($pregunta_data);
            } else {
                echo json_encode(['error' => 'Pregunta no encontrada']);
            }
            break;
            
        case 'add':
        case 'crear':
            // Validar datos requeridos
            $errores = [];
            
            if (empty($_POST['pregunta'])) {
                $errores[] = 'La pregunta es requerida';
            }
            if (empty($_POST['categoria'])) {
                $errores[] = 'La categoría es requerida';
            }
            if (empty($_POST['tema'])) {
                $errores[] = 'El tema es requerido';
            }
            if (empty($_POST['respuesta_correcta'])) {
                $errores[] = 'La respuesta correcta es requerida';
            }
            if (empty($_POST['respuesta_incorrecta_1'])) {
                $errores[] = 'Al menos una respuesta incorrecta es requerida';
            }
            
            if (!empty($errores)) {
                $response['errors'] = $errores;
                $response['message'] = 'Datos inválidos';
                echo json_encode($response);
                break;
            }
            
            // Limpiar y preparar datos
            $pregunta = trim($_POST['pregunta']);
            $categoria = (int)$_POST['categoria'];
            $tema = (int)$_POST['tema'];
            $puntos = 10; // Fijo en 10
            $respuesta_correcta = trim($_POST['respuesta_correcta']);
            $respuesta_incorrecta_1 = trim($_POST['respuesta_incorrecta_1']);
            $respuesta_incorrecta_2 = trim($_POST['respuesta_incorrecta_2'] ?? '');
            $respuesta_incorrecta_3 = trim($_POST['respuesta_incorrecta_3'] ?? '');
            
            try {
                // Iniciar transacción
                global $pdo;
                $pdo->beginTransaction();
                
                // Insertar pregunta
                $queryPregunta = "INSERT INTO preguntas (texto_pregunta, cod_categoria, id_tema, puntos, activa) 
                                 VALUES (:pregunta, :categoria, :tema, :puntos, 1)";
                $stmtPregunta = $pdo->prepare($queryPregunta);
                $stmtPregunta->bindParam(':pregunta', $pregunta);
                $stmtPregunta->bindParam(':categoria', $categoria);
                $stmtPregunta->bindParam(':tema', $tema);
                $stmtPregunta->bindParam(':puntos', $puntos);
                
                if (!$stmtPregunta->execute()) {
                    throw new Exception('Error al insertar la pregunta');
                }
                
                $id_pregunta = $pdo->lastInsertId();
                
                // Insertar respuestas
                $queryRespuesta = "INSERT INTO respuestas (id_pregunta, texto_respuesta, es_correcta) VALUES (:id_pregunta, :respuesta, :es_correcta)";
                $stmtRespuesta = $pdo->prepare($queryRespuesta);
                
                // Respuesta correcta
                $stmtRespuesta->execute([
                    ':id_pregunta' => $id_pregunta,
                    ':respuesta' => $respuesta_correcta,
                    ':es_correcta' => 1
                ]);
                
                // Respuestas incorrectas
                $respuestas_incorrectas = [$respuesta_incorrecta_1];
                if (!empty($respuesta_incorrecta_2)) {
                    $respuestas_incorrectas[] = $respuesta_incorrecta_2;
                }
                if (!empty($respuesta_incorrecta_3)) {
                    $respuestas_incorrectas[] = $respuesta_incorrecta_3;
                }
                
                foreach ($respuestas_incorrectas as $respuesta_incorrecta) {
                    $stmtRespuesta->execute([
                        ':id_pregunta' => $id_pregunta,
                        ':respuesta' => $respuesta_incorrecta,
                        ':es_correcta' => 0
                    ]);
                }
                
                // Confirmar transacción
                $pdo->commit();
                
                $response['success'] = true;
                $response['message'] = 'Pregunta registrada correctamente';
                echo json_encode($response);
                
            } catch (Exception $e) {
                // Revertir transacción en caso de error
                $pdo->rollBack();
                $response['message'] = 'Error al registrar la pregunta: ' . $e->getMessage();
                echo json_encode($response);
            }
            break;
            
        case 'actualizar_estado':
            $id = $_POST['id'];
            $activa = isset($_POST['activa']) ? (int)$_POST['activa'] : 1;
            
            try {
                global $pdo;
                $query = "UPDATE preguntas SET activa = :activa WHERE id_pregunta = :id";
                $stmt = $pdo->prepare($query);
                $resultado = $stmt->execute([':activa' => $activa, ':id' => $id]);
                
                if ($resultado) {
                    $response['success'] = true;
                    $response['message'] = 'Estado de pregunta actualizado correctamente';
                    echo json_encode($response);
                } else {
                    $response['message'] = 'Error al actualizar la pregunta';
                    echo json_encode($response);
                }
            } catch (Exception $e) {
                $response['message'] = 'Error al actualizar la pregunta: ' . $e->getMessage();
                echo json_encode($response);
            }
            break;
            
        default:
            $response['message'] = 'Operación no válida';
            echo json_encode($response);
            break;
    }
    
} else {
    $response['message'] = 'No se recibieron datos';
    echo json_encode($response);
}
?>