<?php
require_once "../bd/conexion.php";
require_once "../bd/claseBD.php";
require_once "temas.php";
require_once "../excepciones/validacionesTemas.php";

header('Content-Type: application/json; charset=utf-8');

// Respuesta JSON estándar
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new DB();
    $operacion = isset($_POST['operacion']) ? $_POST['operacion'] : '';

    // Crear o actualizar tema
    if ($operacion === 'crear' || $operacion === 'actualizar') {
        $id_tema = isset($_POST['id_tema']) ? $_POST['id_tema'] : null;
        $nombre_tema = isset($_POST['nombre_tema']) ? $_POST['nombre_tema'] : '';
        $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';

        $tema = new Tema($nombre_tema, $descripcion, $id_tema);
        $tema->limpiarDatos();
        $errores = $tema->validar();

        if (!empty($errores)) {
            $response['errors'] = $errores;
            $response['message'] = 'Datos inválidos';
            echo json_encode($response);
            exit;
        }

        if ($operacion === 'crear') {
            $resultado = $db->insertarTema($tema->getNombre(), $tema->getDescripcion());
            if ($resultado) {
                $response['success'] = true;
                $response['message'] = 'Tema registrado correctamente';
                echo "ok"; // Mantener compatibilidad con JavaScript actual
            } else {
                $response['message'] = 'Error al registrar el tema';
                echo json_encode($response);
            }
        } elseif ($operacion === 'actualizar') {
            $resultado = $db->actualizarTema($tema->getId(), $tema->getNombre(), $tema->getDescripcion());
            if ($resultado) {
                $response['success'] = true;
                $response['message'] = 'Tema actualizado correctamente';
                echo "modificado"; // Mantener compatibilidad con JavaScript actual
            } else {
                $response['message'] = 'Error al actualizar el tema';
                echo json_encode($response);
            }
        }
        exit;
    }

    // Obtener tema por ID
    if ($operacion === 'obtener') {
        $id_tema = isset($_POST['id_tema']) ? $_POST['id_tema'] : null;
        if ($id_tema) {
            $tema = $db->obtenerTema($id_tema);
            if ($tema) {
                echo json_encode($tema);
            } else {
                echo json_encode(['error' => 'Tema no encontrado']);
            }
        } else {
            echo json_encode(['error' => 'ID de tema no proporcionado']);
        }
        exit;
    }

    // Eliminar tema
    if ($operacion === 'eliminar') {
        $id_tema = isset($_POST['id_tema']) ? $_POST['id_tema'] : null;
        if ($id_tema) {
            $resultado = $db->eliminarTema($id_tema);
            if ($resultado) {
                echo "eliminado";
            } else {
                echo json_encode(['error' => 'Error al eliminar el tema']);
            }
        } else {
            echo json_encode(['error' => 'ID de tema no proporcionado']);
        }
        exit;
    }

    // Si no se reconoce la operación
    echo json_encode(['error' => 'Operación no válida']);
    exit;
} else {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}
