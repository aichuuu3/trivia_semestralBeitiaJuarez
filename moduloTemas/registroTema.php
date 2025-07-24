<?php
require("conexion.php");
require("producto.php");

// Respuesta JSON estándar
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

if (isset($_POST)) {
    //crea instancias de las clases
    $db = new DB();
    $producto = new Producto(
        $_POST['codigo'] ?? '',
        $_POST['producto'] ?? '',
        $_POST['precio'] ?? 0,
        $_POST['cantidad'] ?? 0,
        $_POST['idp'] ?? null
    );
    
    //Limpia y validar datos
    $producto->limpiarDatos();
    $errores = $producto->validar();
    
    if (!empty($errores)) {
        $response['errors'] = $errores;
        $response['message'] = 'Datos inválidos';
        echo json_encode($response);
        exit;
    }
    
    //Determinar operación con switch
    $operacion = empty($_POST['idp']) ? 'crear' : 'actualizar';
    
    switch ($operacion) {
        case 'crear':
            $resultado = $db->insertarProducto(
                $producto->getCodigo(),
                $producto->getProducto(),
                $producto->getPrecio(),
                $producto->getCantidad()
            );
            
            if ($resultado) {
                $response['success'] = true;
                $response['message'] = 'Producto registrado correctamente';
                echo "ok"; // Mantener compatibilidad con JavaScript actual
            } else {
                $response['message'] = 'Error al registrar el producto';
                echo json_encode($response);
            }
            break;
            
        case 'actualizar':
            $id = $_POST['idp'];
            $resultado = $db->actualizarProducto(
                $id,
                $producto->getCodigo(),
                $producto->getProducto(),
                $producto->getPrecio(),
                $producto->getCantidad()
            );
            
            if ($resultado) {
                $response['success'] = true;
                $response['message'] = 'Producto actualizado correctamente';
                echo "modificado"; // Mantener compatibilidad con JavaScript actual
            } else {
                $response['message'] = 'Error al actualizar el producto';
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
