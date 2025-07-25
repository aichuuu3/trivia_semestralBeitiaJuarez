<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - AhaSlides</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/IndexPreguntas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-star"></i>
            </div>
            <span>ReichMind</span>
        </div>
    </header>
    <?php
    session_start();
    if (!isset($_SESSION['rol'])) {
        exit;
    }
    ?>
    <div class="layout-flex">
        <?php
        // Sidebar dinámico
        if ($_SESSION['rol'] === 'admin') {
            echo '<div class="sidebar">';
            include '../sidebar/sb.php';
            echo '</div>';
        } elseif ($_SESSION['rol'] === 'colaborador') {
            echo '<div class="sidebar">';
            include '../sidebar/sbcolaborador.php';
            echo '</div>';
        }
        ?>
        <div class="main-container">
            <h1 class="mb-4"><span class="icono-pregunta">❓</span> Sistema de Gestión de Preguntas</h1>
            <div class="row">
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h3 class="text-center mb-0">Registro de preguntas</h3>
                        </div>
                        <div class="card-body">
                            <form action="registroPreguntas.php" method="post" id="frm">
                                <div class="form-group mb-3">
                                    <label for="">Pregunta</label>
                                    <textarea name="pregunta" placeholder="Escribe tu pregunta aquí..." required class="form-control" rows="3"></textarea>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="">Categoría</label>
                                    <select name="categoria" required class="form-control">
                                        <option value="">Selecciona una categoría</option>
                                        <option value="3">Principiante</option>
                                        <option value="2">Novato</option>
                                        <option value="1">Experto</option>
                                    </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="">Tema</label>
                                        <select name="tema" required class="form-control">
                                            <option value="">Selecciona un tema</option>
                                            <?php
                                            require_once '../bd/conexion.php';
                                            try {
                                                $queryTemas = "SELECT id_tema, nombre_tema FROM temas ORDER BY nombre_tema";
                                                $stmtTemas = $pdo->prepare($queryTemas);
                                                $stmtTemas->execute();
                                                $temas = $stmtTemas->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($temas as $tema) {
                                                    echo "<option value='" . $tema['id_tema'] . "'>" . htmlspecialchars($tema['nombre_tema']) . "</option>";
                                                }
                                            } catch (Exception $e) {
                                                echo "<option value=''>Error al cargar temas</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="">Puntos</label>
                                        <input type="number" name="puntos" value="10" readonly class="form-control">
                                        <small class="form-text text-muted">Los puntos son fijos en 10 para todas las preguntas</small>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="">Respuesta Correcta</label>
                                        <input type="text" name="respuesta_correcta" placeholder="Escribe la respuesta correcta" required class="form-control">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Respuesta Incorrecta 1</label>
                                        <input type="text" name="respuesta_incorrecta_1" placeholder="Primera respuesta incorrecta" required class="form-control">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Respuesta Incorrecta 2 (Opcional)</label>
                                        <input type="text" name="respuesta_incorrecta_2" placeholder="Segunda respuesta incorrecta" class="form-control">
                                        <small class="form-text text-muted">Para preguntas de verdadero/falso, deja este campo vacío</small>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Respuesta Incorrecta 3 (Opcional)</label>
                                        <input type="text" name="respuesta_incorrecta_3" placeholder="Tercera respuesta incorrecta" class="form-control">
                                    </div>
                                    <div class="form-group mb-0">
                                        <input type="button" value="Añadir Pregunta" id="registrar" class="btn btn-primary btn-block">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h3 class="mb-0">Listado de preguntas</h3>
                            </div>
                            <div class="card-body">
                                <!-- Formulario de búsqueda, actualización y eliminación eliminado. Solo agregar preguntas habilitado. -->
                                <div class="table-responsive" style="max-width:100%; overflow-x:auto;">
                                    <table class="table table-hover align-middle" style="min-width:700px;">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Pregunta</th>
                                                <th>Categoría</th>
                                                <th>Tema</th>
                                                <th>Respuesta Correcta</th>
                                                <th>Respuestas Incorrectas</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody id="resultado">
                                            <?php
                                            require_once '../bd/conexion.php';
                                            try {
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
                                                if (!empty($preguntas)) {
                                                    foreach ($preguntas as $pregunta) {
                                                        $estado = $pregunta['activa'] ? 'Activa' : 'Inactiva';
                                                        $estadoClass = $pregunta['activa'] ? 'text-success' : 'text-danger';
                                                        $categoriaClass = '';
                                                        switch ($pregunta['nombre_categoria']) {
                                                            case 'Principiante': $categoriaClass = 'badge-success'; break;
                                                            case 'Novato': $categoriaClass = 'badge-warning'; break;
                                                            case 'Experto': $categoriaClass = 'badge-danger'; break;
                                                            default: $categoriaClass = 'badge-primary';
                                                        }
                                                        $temaClass = '';
                                                        switch ($pregunta['nombre_tema']) {
                                                            case 'PHP': $temaClass = 'badge-info'; break;
                                                            case 'Laravel': $temaClass = 'badge-dark'; break;
                                                            case 'CRUD': $temaClass = 'badge-secondary'; break;
                                                            default: $temaClass = 'badge-light';
                                                        }
                                                        echo "<tr>";
                                                        echo "<td>" . htmlspecialchars($pregunta['id_pregunta']) . "</td>";
                                                        echo "<td class='text-wrap pregunta-cell'>" . htmlspecialchars($pregunta['texto_pregunta']) . "</td>";
                                                        echo "<td><span class='badge $categoriaClass'>" . htmlspecialchars($pregunta['nombre_categoria']) . "</span></td>";
                                                        echo "<td><span class='badge $temaClass'>" . htmlspecialchars($pregunta['nombre_tema']) . "</span></td>";
                                                        echo "<td><strong class='text-success'>" . htmlspecialchars($pregunta['respuesta_correcta']) . "</strong></td>";
                                                        echo "<td class='text-wrap respuestas-cell'>" . htmlspecialchars($pregunta['respuestas_incorrectas']) . "</td>";
                                                        echo "<td><span class='$estadoClass'><i class='fas fa-circle'></i> $estado</span></td>";
                                                        echo "</tr>";
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='7' class='text-center text-muted'><i class='fas fa-info-circle'></i> No hay preguntas registradas</td></tr>";
                                                }
                                            } catch (Exception $e) {
                                                echo "<tr><td colspan='7' class='text-center text-danger'><i class='fas fa-exclamation-triangle'></i> Error al cargar las preguntas: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <!-- Script para el menú lateral -->
    <script>
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.addEventListener('click', function(e) {
                // Si el enlace tiene href="#", prevenir la navegación
                if (this.getAttribute('href') === '#') {
                    e.preventDefault();
                }
                
                // Remover clase active de todos los enlaces
                document.querySelectorAll('.sidebar-menu a').forEach(l => l.classList.remove('active'));
                // Agregar clase active al enlace clickeado
                this.classList.add('active');
                
                const section = this.querySelector('span:last-child').textContent;
                console.log(`Navegando a: ${section}`);
            });
        });

        // AJAX para registrar pregunta
        document.getElementById('registrar').addEventListener('click', function() {
            const form = document.getElementById('frm');
            const formData = new FormData(form);
            fetch('registroPreguntas.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message
                    });
                    form.reset();
                    // Recargar la tabla de preguntas
                    recargarPreguntas();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo registrar la pregunta.'
                });
            });
        });

        function recargarPreguntas() {
            fetch('indexPreguntas.php?ajax=1')
                .then(response => response.text())
                .then(html => {
                    // Extraer solo el tbody de la tabla de preguntas
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    const nuevoTbody = tempDiv.querySelector('#resultado');
                    if (nuevoTbody) {
                        document.getElementById('resultado').innerHTML = nuevoTbody.innerHTML;
                    }
                });
        }
    </script>
</body>

</html>