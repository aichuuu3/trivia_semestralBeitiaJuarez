<?php
// Mostrar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("../bd/conexion.php");
require("../bd/claseBD.php");

// Respuesta JSON estándar
$response = [
    'status' => 'error',
    'message' => ''
];

if (!empty($_POST)) {
    //crea instancia de la clase DB
    $db = new DB();
    
    // Verificar si todos los campos requeridos están presentes
    $pregunta = trim($_POST['pregunta'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $tema = trim($_POST['tema'] ?? '');
    $puntos = trim($_POST['puntos'] ?? '10');
    $respuesta_correcta = trim($_POST['respuesta_correcta'] ?? '');
    $respuesta_incorrecta_1 = trim($_POST['respuesta_incorrecta_1'] ?? '');
    $respuesta_incorrecta_2 = trim($_POST['respuesta_incorrecta_2'] ?? '');
    $respuesta_incorrecta_3 = trim($_POST['respuesta_incorrecta_3'] ?? '');
    
    // Validaciones básicas
    if (empty($pregunta)) {
        $response['message'] = 'La pregunta es requerida';
        echo json_encode($response);
        exit;
    }
    
    if (empty($categoria)) {
        $response['message'] = 'La categoría es requerida';
        echo json_encode($response);
        exit;
    }
    
    if (empty($tema)) {
        $response['message'] = 'El tema es requerido';
        echo json_encode($response);
        exit;
    }
    
    if (empty($respuesta_correcta)) {
        $response['message'] = 'La respuesta correcta es requerida';
        echo json_encode($response);
        exit;
    }
    
    if (empty($respuesta_incorrecta_1)) {
        $response['message'] = 'Al menos una respuesta incorrecta es requerida';
        echo json_encode($response);
        exit;
    }
    
    try {
        global $pdo;
        
        // Iniciar transacción
        $pdo->beginTransaction();
        
        // Insertar pregunta
        $queryPregunta = "INSERT INTO preguntas (texto_pregunta, puntos, cod_categoria, id_tema, activa) 
                         VALUES (:pregunta, :puntos, :categoria, :tema, 1)";
        $stmtPregunta = $pdo->prepare($queryPregunta);
        $stmtPregunta->bindParam(':pregunta', $pregunta);
        $stmtPregunta->bindParam(':puntos', $puntos, PDO::PARAM_INT);
        $stmtPregunta->bindParam(':categoria', $categoria, PDO::PARAM_INT);
        $stmtPregunta->bindParam(':tema', $tema, PDO::PARAM_INT);
        
        if (!$stmtPregunta->execute()) {
            throw new Exception("Error al insertar la pregunta");
        }
        
        // Obtener ID de la pregunta recién insertada
        $pregunta_id = $pdo->lastInsertId();
        
        // Insertar respuesta correcta
        $queryRespuestaCorrecta = "INSERT INTO respuestas (id_pregunta, texto_respuesta, es_correcta) 
                                  VALUES (:pregunta_id, :respuesta, 1)";
        $stmtCorrecta = $pdo->prepare($queryRespuestaCorrecta);
        $stmtCorrecta->bindParam(':pregunta_id', $pregunta_id, PDO::PARAM_INT);
        $stmtCorrecta->bindParam(':respuesta', $respuesta_correcta);
        
        if (!$stmtCorrecta->execute()) {
            throw new Exception("Error al insertar la respuesta correcta");
        }
        
        // Insertar respuestas incorrectas
        $queryRespuestaIncorrecta = "INSERT INTO respuestas (id_pregunta, texto_respuesta, es_correcta) 
                                    VALUES (:pregunta_id, :respuesta, 0)";
        $stmtIncorrecta = $pdo->prepare($queryRespuestaIncorrecta);
        
        // Primera respuesta incorrecta (obligatoria)
        $stmtIncorrecta->bindParam(':pregunta_id', $pregunta_id, PDO::PARAM_INT);
        $stmtIncorrecta->bindParam(':respuesta', $respuesta_incorrecta_1);
        if (!$stmtIncorrecta->execute()) {
            throw new Exception("Error al insertar la primera respuesta incorrecta");
        }
        
        // Segunda respuesta incorrecta (opcional)
        if (!empty($respuesta_incorrecta_2)) {
            $stmtIncorrecta->bindParam(':pregunta_id', $pregunta_id, PDO::PARAM_INT);
            $stmtIncorrecta->bindParam(':respuesta', $respuesta_incorrecta_2);
            if (!$stmtIncorrecta->execute()) {
                throw new Exception("Error al insertar la segunda respuesta incorrecta");
            }
        }
        
        // Tercera respuesta incorrecta (opcional)
        if (!empty($respuesta_incorrecta_3)) {
            $stmtIncorrecta->bindParam(':pregunta_id', $pregunta_id, PDO::PARAM_INT);
            $stmtIncorrecta->bindParam(':respuesta', $respuesta_incorrecta_3);
            if (!$stmtIncorrecta->execute()) {
                throw new Exception("Error al insertar la tercera respuesta incorrecta");
            }
        }
        
        // Confirmar transacción
        $pdo->commit();
        
        $response['status'] = 'success';
        $response['message'] = 'Pregunta registrada correctamente';
        
    } catch (Exception $e) {
        // Deshacer transacción en caso de error
        $pdo->rollBack();
        $response['message'] = 'Error al registrar la pregunta: ' . $e->getMessage();
    }
    
} else {
    $response['message'] = 'No se recibieron datos';
}

// Enviar respuesta JSON
echo json_encode($response);
?>