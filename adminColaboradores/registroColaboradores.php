<?php
require("../bd/conexion.php");
require("../bd/claseBD.php");
require("../excepciones/validacionesColaborador.php");
require("colaborador.php");

// Respuesta JSON estándar
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

if (!empty($_POST)) {
    //crea instancia de la clase DB
    $db = new DB();
    
    // determinar operación xd
    $operacion = $_POST['operacion'] ?? (empty($_POST['id']) ? 'crear' : 'actualizar');
    
    // validar que la operación sea válida
    $erroresOperacion = ValidacionesColaborador::validarOperacion($operacion, $_POST);
    if (!empty($erroresOperacion)) {
        $response['errors'] = $erroresOperacion;
        $response['message'] = 'Operación no válida';
        echo json_encode($response);
        exit;
    }
    
    // switch para todas las operaciones
    switch ($operacion) {
        case 'eliminar':
            $id = $_POST['id'];
            $resultado = $db->eliminarColaborador($id);
            
            if ($resultado) {
                $response['success'] = true;
                $response['message'] = 'Colaborador eliminado correctamente';
                echo "eliminado";
            } else {
                $response['message'] = 'Error al eliminar el colaborador';
                echo json_encode($response);
            }
            break;
            
        case 'obtener':
            $id = $_POST['id'];
            $colaborador_data = $db->obtenerColaborador($id);
            
            if ($colaborador_data) {
                // no devolver la contraseña por seguridad xd
                unset($colaborador_data['password']);
                echo json_encode($colaborador_data);
            } else {
                echo json_encode(['error' => 'Colaborador no encontrado']);
            }
            break;
            
        case 'crear':
        case 'actualizar':
            // validar datos completos para crear y actualizar
            $esCreacion = ($operacion === 'crear');
            $resultadoValidacion = ValidacionesColaborador::validarColaborador($_POST, $db, $esCreacion);
            
            if (!empty($resultadoValidacion['errores'])) {
                $response['errors'] = $resultadoValidacion['errores'];
                $response['message'] = 'Datos inválidos';
                echo json_encode($response);
                break;
            }
            
            // usar los datos ya validados y limpiados
            $datosLimpios = $resultadoValidacion['datos'];
            
            if ($operacion === 'crear') {
                $resultado = $db->insertarColaborador(
                    $datosLimpios['email'],
                    $datosLimpios['nombre'],
                    $datosLimpios['password']
                );
                
                if ($resultado) {
                    $response['success'] = true;
                    $response['message'] = 'Colaborador registrado correctamente';
                    echo "ok";
                } else {
                    $response['message'] = 'Error al registrar el colaborador';
                    echo json_encode($response);
                }
            } else { // actualizar
                $id = $datosLimpios['id'];
                $resultado = $db->actualizarColaborador(
                    $id,
                    $datosLimpios['email'],
                    $datosLimpios['nombre'],
                    $datosLimpios['password']
                );
                
                if ($resultado) {
                    $response['success'] = true;
                    $response['message'] = 'Colaborador actualizado correctamente';
                    echo "modificado";
                } else {
                    $response['message'] = 'Error al actualizar el colaborador';
                    echo json_encode($response);
                }
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