<?php
ob_start(); // Iniciar output buffering

require_once "../bd/conexion.php";
require_once "../bd/claseBD.php";
require_once "temas.php";
require_once "../excepciones/validacionesTemas.php";

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
        header('Content-Type: text/plain; charset=utf-8');
        
        $id_tema = isset($_POST['id_tema']) ? $_POST['id_tema'] : null;
        $nombre_tema = isset($_POST['nombre_tema']) ? $_POST['nombre_tema'] : '';
        $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';

        $tema = new Tema($nombre_tema, $descripcion, $id_tema);
        $tema->limpiarDatos();
        $errores = $tema->validar();

        if (!empty($errores)) {
            header('Content-Type: application/json; charset=utf-8');
            $response['errors'] = $errores;
            $response['message'] = 'Datos inválidos';
            echo json_encode($response);
            exit;
        }

        if ($operacion === 'crear') {
            $resultado = $db->insertarTema($tema->getNombre(), $tema->getDescripcion());
            if ($resultado) {
                ob_clean(); // Limpiar cualquier salida previa
                echo "ok";
                exit;
            } else {
                ob_clean(); // Limpiar cualquier salida previa
                header('Content-Type: application/json; charset=utf-8');
                $response['message'] = 'Error al registrar el tema';
                echo json_encode($response);
                exit;
            }
        } elseif ($operacion === 'actualizar') {
            $resultado = $db->actualizarTema($tema->getId(), $tema->getNombre(), $tema->getDescripcion());
            if ($resultado) {
                ob_clean(); // Limpiar cualquier salida previa
                echo "modificado";
                exit;
            } else {
                ob_clean(); // Limpiar cualquier salida previa
                header('Content-Type: application/json; charset=utf-8');
                $response['message'] = 'Error al actualizar el tema';
                echo json_encode($response);
                exit;
            }
        }
        exit;
    }

    // Obtener tema por ID
    if ($operacion === 'obtener') {
        header('Content-Type: application/json; charset=utf-8');
        
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
        header('Content-Type: text/plain; charset=utf-8');
        
        $id_tema = isset($_POST['id_tema']) ? $_POST['id_tema'] : null;
        if ($id_tema) {
            $resultado = $db->eliminarTema($id_tema);
            if ($resultado) {
                ob_clean(); // Limpiar cualquier salida previa
                echo "eliminado";
                exit;
            } else {
                ob_clean(); // Limpiar cualquier salida previa
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['error' => 'Error al eliminar el tema']);
                exit;
            }
        } else {
            ob_clean(); // Limpiar cualquier salida previa
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'ID de tema no proporcionado']);
            exit;
        }
        exit;
    }

    // Si no se reconoce la operación
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Operación no válida']);
    exit;
} else {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}
