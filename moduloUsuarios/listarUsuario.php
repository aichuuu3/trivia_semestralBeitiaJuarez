<?php
try {
    $data = file_get_contents("php://input");
    require "../bd/conexion.php";
    require "../bd/claseBD.php";
    
    $db = new DB();
    $resultado = $db->listarUsuariosExtra($data);
    
    if (empty($resultado)) {
        echo "<tr><td colspan='6' class='text-center'>No se encontraron usuarios</td></tr>";
    } else {
        foreach ($resultado as $data) {
            // formatear fechas para que se vean bonitas xd
            $fechaFormateada = date('d/m/Y', strtotime($data['fecha_registro']));
            $horaFormateada = date('H:i', strtotime($data['hora_inicio_actividad']));
            
            echo "<tr>
                    <td>" . htmlspecialchars($data['id']) . "</td>
                    <td>" . htmlspecialchars($data['nombre']) . "</td>
                    <td>" . htmlspecialchars($data['email']) . "</td>
                    <td>" . $fechaFormateada . "</td>
                    <td>" . $horaFormateada . "</td>
                    <td>
                        <button type='button' class='btn btn-success btn-sm' onclick='Editar(\"" . $data['id'] . "\")'>Editar</button>
                        <button type='button' class='btn btn-danger btn-sm' onclick='Eliminar(\"" . $data['id'] . "\")'>Eliminar</button>
                    </td>        
                </tr>";
        }
    }
} catch (Exception $e) {
    // si hay error, que al menos no rompa la tabla xd
    echo "<tr><td colspan='6' class='text-center text-danger'>Error al cargar datos</td></tr>";
}
?>
