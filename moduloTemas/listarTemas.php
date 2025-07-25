<?php
try {
    $data = file_get_contents("php://input");
    require "../bd/conexion.php";
    require "../bd/claseBD.php";
    
    $db = new DB();
    $resultado = $db->listarTemas();
    
    if (empty($resultado)) {
        echo "<tr><td colspan='2' class='text-center'>No se encontraron temas</td></tr>";
    } else {
        foreach ($resultado as $tema) {
            echo "<tr>
                    <td>" . htmlspecialchars($tema['id_tema']) . "</td>
                    <td>" . htmlspecialchars($tema['nombre_tema']) . "</td>
                <td>" . htmlspecialchars($tema['descripcion']) . "</td>
                </tr>";
        }
    }
} catch (Exception $e) {
    // si hay error, que al menos no rompa la tabla xd
    echo "<tr><td colspan='6' class='text-center text-danger'>Error al cargar datos</td></tr>";
}
?>
