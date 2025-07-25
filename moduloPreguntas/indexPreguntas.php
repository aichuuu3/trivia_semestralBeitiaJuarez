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

    <div class="container-fluid mt-5">
        <!-- Sidebar comentado temporalmente para pruebas -->
        <!-- Sidebar -->
        <?php include '../sidebar/sb.php'; ?>

        <!-- Main Content -->
         <main class="main-content">
            <div class="container-fluid">
        <h1>❓ Sistema de Gestión de Preguntas</h1>
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="text-center">Añadir Pregunta</h3>
                    </div>
                    <div class="card-body">
                        <form action="registroPreguntas.php" method="post" id="frm">
                            <div class="form-group">
                                <label for="">Pregunta</label>
                                <textarea name="pregunta" placeholder="Escribe tu pregunta aquí..." required class="form-control" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="">Categoría</label>
                                <select name="categoria" required class="form-control">
                                    <option value="">Selecciona una categoría</option>
                                    <option value="3">Principiante</option>
                                    <option value="2">Novato</option>
                                    <option value="1">Experto</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">Tema</label>
                                <select name="tema" required class="form-control">
                                    <option value="">Selecciona un tema</option>
                                    <?php
                                    // Cargar temas desde la base de datos
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

                            <div class="form-group">
                                <label for="">Puntos</label>
                                <input type="number" name="puntos" value="10" readonly class="form-control">
                                <small class="form-text text-muted">Los puntos son fijos en 10 para todas las preguntas</small>
                            </div>

                            <div class="form-group">
                                <label for="">Respuesta Correcta</label>
                                <input type="text" name="respuesta_correcta" placeholder="Escribe la respuesta correcta" required class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="">Respuesta Incorrecta 1</label>
                                <input type="text" name="respuesta_incorrecta_1" placeholder="Primera respuesta incorrecta" required class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="">Respuesta Incorrecta 2 (Opcional)</label>
                                <input type="text" name="respuesta_incorrecta_2" placeholder="Segunda respuesta incorrecta" class="form-control">
                                <small class="form-text text-muted">Para preguntas de verdadero/falso, deja este campo vacío</small>
                            </div>

                            <div class="form-group">
                                <label for="">Respuesta Incorrecta 3 (Opcional)</label>
                                <input type="text" name="respuesta_incorrecta_3" placeholder="Tercera respuesta incorrecta" class="form-control">
                            </div>

                            <div class="form-group">
                                <input type="button" value="Añadir Pregunta" id="registrar" class="btn btn-primary btn-block">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-lg-6 ml-auto">
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="buscar">Buscar:</label>
                                <input type="text" name="buscar" id="buscar" placeholder="Buscar pregunta..." class="form-control">
                            </div>
                        </form>
                    </div>
                </div>
                <table class="table table-hover table-responsive">
                    <thead class="thead-dark">
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
                        // Incluir conexión a la base de datos
                        require_once '../bd/conexion.php';
                        
                        try {
                            // Consulta para obtener preguntas con sus respuestas y temas
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
                                    
                                    // Determinar color del badge de categoría
                                    $categoriaClass = '';
                                    switch ($pregunta['nombre_categoria']) {
                                        case 'Principiante':
                                            $categoriaClass = 'badge-success';
                                            break;
                                        case 'Novato':
                                            $categoriaClass = 'badge-warning';
                                            break;
                                        case 'Experto':
                                            $categoriaClass = 'badge-danger';
                                            break;
                                        default:
                                            $categoriaClass = 'badge-primary';
                                    }
                                    
                                    // Determinar color del badge de tema
                                    $temaClass = '';
                                    switch ($pregunta['nombre_tema']) {
                                        case 'PHP':
                                            $temaClass = 'badge-info';
                                            break;
                                        case 'Laravel':
                                            $temaClass = 'badge-dark';
                                            break;
                                        case 'CRUD':
                                            $temaClass = 'badge-secondary';
                                            break;
                                        default:
                                            $temaClass = 'badge-light';
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
        </main>
    </div>

    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <!-- Script para manejo dinámico del formulario -->
    <script>
        console.log('Script cargado correctamente');
        
        // Variables globales para el estado del formulario
        let currentOperation = 'add';
        let selectedQuestionId = null;

        // Función para cambiar la operación del formulario
        function changeOperation(operation) {
            console.log('Función changeOperation llamada con:', operation);
            
            try {
                currentOperation = operation;
                
                // Actualizar indicador visual si existe
                const estadoElement = document.getElementById('estado-operacion');
                if (estadoElement) {
                    estadoElement.textContent = operation;
                }
                
                // Actualizar botones activos
                console.log('Actualizando botones...');
                document.querySelectorAll('.btn-group .btn').forEach(btn => {
                    btn.classList.remove('active');
                    console.log('Removiendo active de:', btn.id);
                });
                
                const btnElement = document.getElementById(`btn-${operation}`);
                if (btnElement) {
                    btnElement.classList.add('active');
                    console.log('Botón activado:', `btn-${operation}`);
                } else {
                    console.error('No se encontró el botón:', `btn-${operation}`);
                }
                
                // Actualizar campo oculto de operación
                const operationField = document.getElementById('operation');
                if (operationField) {
                    operationField.value = operation;
                    console.log('Campo operation actualizado a:', operation);
                } else {
                    console.error('No se encontró el campo operation');
                }
                
                // Mostrar/ocultar campos según la operación
                console.log('Llamando a toggleFormFields...');
                toggleFormFields(operation);
                
                // Actualizar el texto del botón principal
                console.log('Llamando a updateSubmitButton...');
                updateSubmitButton(operation);
                
                // Limpiar formulario si es operación de añadir
                if (operation === 'add') {
                    console.log('Limpiando formulario...');
                    clearForm();
                }
                
                console.log('changeOperation completado exitosamente');
                
            } catch (error) {
                console.error('Error en changeOperation:', error);
            }
        }

        // Función para mostrar/ocultar campos del formulario
        function toggleFormFields(operation) {
            console.log('toggleFormFields iniciada con operación:', operation);
            
            try {
                const fieldsToControl = [
                    'grupo-categoria', 
                    'grupo-tema', 
                    'grupo-pregunta', 
                    'grupo-respuesta-correcta', 
                    'grupo-respuesta-incorrecta-1', 
                    'grupo-respuesta-incorrecta-2', 
                    'grupo-respuesta-incorrecta-3'
                ];
                
                const selectorGroup = document.getElementById('grupo-seleccionar-pregunta');
                console.log('Selector group encontrado:', !!selectorGroup);
                
                if (operation === 'add') {
                    console.log('Configurando para operación ADD');
                    
                    // Mostrar todos los campos para añadir
                    fieldsToControl.forEach(field => {
                        const element = document.getElementById(field);
                        if (element) {
                            element.style.display = 'block';
                            console.log('Mostrando campo:', field);
                        } else {
                            console.error('Campo no encontrado:', field);
                        }
                    });
                    
                    // Ocultar selector de pregunta
                    if (selectorGroup) {
                        selectorGroup.style.display = 'none';
                        console.log('Ocultando selector de pregunta');
                    }
                    
                } else if (operation === 'edit') {
                    console.log('Configurando para operación EDIT');
                    
                    // Mostrar selector de pregunta
                    if (selectorGroup) {
                        selectorGroup.style.display = 'block';
                        console.log('Mostrando selector de pregunta');
                    }
                    
                    // Mostrar todos los campos
                    fieldsToControl.forEach(field => {
                        const element = document.getElementById(field);
                        if (element) {
                            element.style.display = 'block';
                            console.log('Mostrando campo:', field);
                        }
                    });
                    
                } else if (operation === 'delete') {
                    console.log('Configurando para operación DELETE');
                    
                    // Mostrar solo selector de pregunta
                    if (selectorGroup) {
                        selectorGroup.style.display = 'block';
                        console.log('Mostrando selector de pregunta');
                    }
                    
                    // Ocultar campos de entrada
                    fieldsToControl.forEach(field => {
                        const element = document.getElementById(field);
                        if (element) {
                            element.style.display = 'none';
                            console.log('Ocultando campo:', field);
                        }
                    });
                }
                
                console.log('toggleFormFields completado exitosamente');
                
            } catch (error) {
                console.error('Error en toggleFormFields:', error);
            }
        }

        // Función para actualizar el botón de envío
        function updateSubmitButton(operation) {
            console.log('updateSubmitButton llamada con:', operation);
            
            try {
                const button = document.getElementById('registrar');
                const titulo = document.getElementById('titulo-operacion');
                
                const texts = {
                    'add': 'Añadir Pregunta',
                    'edit': 'Actualizar Pregunta',
                    'delete': 'Eliminar Pregunta'
                };
                
                const titles = {
                    'add': 'Añadir Pregunta',
                    'edit': 'Editar Pregunta',
                    'delete': 'Eliminar Pregunta'
                };
                
                const classes = {
                    'add': 'btn-primary',
                    'edit': 'btn-warning',
                    'delete': 'btn-danger'
                };
                
                if (button) {
                    button.value = texts[operation];
                    button.className = `btn ${classes[operation]} btn-block`;
                    console.log('Botón actualizado:', texts[operation]);
                } else {
                    console.error('Botón registrar no encontrado');
                }
                
                if (titulo) {
                    titulo.textContent = titles[operation];
                    console.log('Título actualizado:', titles[operation]);
                } else {
                    console.error('Título no encontrado');
                }
                
            } catch (error) {
                console.error('Error en updateSubmitButton:', error);
            }
        }
            };
            
            const classes = {
                'add': 'btn-primary',
                'edit': 'btn-warning',
                'delete': 'btn-danger'
            };
            
            if (button) {
                button.value = texts[operation];
                button.className = `btn ${classes[operation]} btn-block`;
                console.log('Botón actualizado:', texts[operation]);
            }
            
            if (titulo) {
                titulo.textContent = titles[operation];
                console.log('Título actualizado:', titles[operation]);
            }
        }

        // Función para limpiar el formulario
        function clearForm() {
            document.getElementById('formPreguntas').reset();
            document.getElementById('pregunta-id').value = '';
            selectedQuestionId = null;
        }

        // Función para cargar datos de pregunta seleccionada
        function loadQuestionData(questionId) {
            if (!questionId) return;
            
            selectedQuestionId = questionId;
            document.getElementById('pregunta-id').value = questionId;
            
            // Si es edición, cargar los datos de la pregunta
            if (currentOperation === 'edit') {
                loadQuestionDetails(questionId); // Función definida en preguntasManager.js
            }
        }

        // Event listeners para los botones de operación
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM cargado, inicializando formulario...');
            
            // Configurar selector de pregunta
            const selectPregunta = document.getElementById('select-pregunta');
            if (selectPregunta) {
                selectPregunta.addEventListener('change', function() {
                    loadQuestionData(this.value);
                });
                console.log('Event listener para selector de pregunta configurado');
            } else {
                console.error('No se encontró el selector de pregunta');
            }
            
            // Inicializar formulario en modo "añadir"
            console.log('Inicializando en modo añadir...');
            changeOperation('add');
        });
    </script>

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
    </script>
</body>

</html>