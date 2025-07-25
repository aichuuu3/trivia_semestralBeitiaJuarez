<?php
try {
    $data = file_get_contents("php://input");
    $busqueda = trim($data);
    require "../bd/conexion.php";
    require "../bd/claseBD.php";
    
    $db = new DB();
    $temas = $db->listarTemas();
    
    if ($busqueda !== '') {
        $temas = array_filter($temas, function($tema) use ($busqueda) {
            return stripos($tema['nombre_tema'], $busqueda) !== false ||
                   stripos($tema['descripcion'], $busqueda) !== false ||
                   stripos((string)$tema['id_tema'], $busqueda) !== false;
        });
    }
    
    if (empty($temas)) {
        echo "<tr><td colspan='4' class='text-center'>No se encontraron temas</td></tr>";
    } else {
        foreach ($temas as $tema) {
            echo "<tr>
                    <td>" . htmlspecialchars($tema['id_tema']) . "</td>
                    <td>" . htmlspecialchars($tema['nombre_tema']) . "</td>
                    <td>" . htmlspecialchars($tema['descripcion']) . "</td>
                    <td>
                        <button class='btn btn-success btn-sm' onclick='editarTema(" . $tema['id_tema'] . ")'>Editar</button>
                        <button class='btn btn-danger btn-sm' onclick='eliminarTema(" . $tema['id_tema'] . ")'>Eliminar</button>
                    </td>
                </tr>";
        }
    }
} catch (Exception $e) {
    // si hay error, que al menos no rompa la tabla xd
    echo "<tr><td colspan='6' class='text-center text-danger'>Error al cargar datos</td></tr>";
}
?>
