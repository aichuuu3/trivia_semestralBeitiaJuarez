<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - AhaSlides</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/IndexPreguntas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Asegurar que los elementos sean visibles */
        .form-group {
            display: block !important;
            margin-bottom: 1rem;
        }
        
        .btn-group {
            display: flex !important;
        }
        
        .card {
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
<?php
// Incluir conexión a la base de datos
require_once '../bd/conexion.php';
?>
    <!-- Header comentado temporalmente para pruebas -->
    <!--
    <header class="header">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-star"></i>
            </div>
            <span>ReichMind</span>
        </div>
    </header>
    -->

    <div class="container-fluid mt-5">
        <!-- Sidebar comentado temporalmente para pruebas -->
        <!-- Sidebar -->
        <nav class="sidebar">
            <ul class="sidebar-menu">
                <li>
                    <a href="../administrativos/ventanaAdmin.php" >
                        <span class="icon"><i class="fas fa-home"></i></span>
                        <span>Inicio</span>
                    </a>
                </li>
                <li>
                    <a href="../moduloUsuarios/indexUsuario.php">
                        <span class="icon"><i class="fas fa-users"></i></span>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="../moduloTemas/indexTema.php">
                        <span class="icon"><i class="fas fa-file-alt"></i></span>
                        <span>Categorias</span>
                    </a>
                </li>
                <li>
                    <a href="../moduloPreguntas/indexPreguntas.php" class="active">
                        <span class="icon"><i class="fas fa-question-circle"></i></span>
                        <span>Preguntas</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="icon"><i class="fas fa-calendar"></i></span>
                        <span>Mensajes y notificaciones</span>
                    </a>
                </li>
                <li>
                    <a href="../adminColaboradores/indexColaborador.php">
                        <span class="icon"><i class="fas fa-plus"></i></span>
                        <span>Administrar Colaboradores</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="icon"><i class="fas fa-cog"></i></span>
                        <span>Configuración</span>
                    </a>
                </li>
            </ul>
        </nav> 

        <!-- Main Content -->
         <main class="main-content">
            <div class="container">
        <h1>❓ Sistema de Gestión de Preguntas</h1>
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="text-center" id="titulo-operacion">Añadir Pregunta</h3>
                    </div>
                    <div class="card-body">
                        <!-- Botones de operación -->
                        <div class="btn-group btn-group-toggle w-100 mb-3" data-toggle="buttons">
                            <button type="button" class="btn btn-outline-success active" id="btn-add" onclick="changeOperation('add')">
                                <i class="fas fa-plus"></i> Añadir
                            </button>
                            <button type="button" class="btn btn-outline-warning" id="btn-edit" onclick="changeOperation('edit')">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button type="button" class="btn btn-outline-danger" id="btn-delete" onclick="changeOperation('delete')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>

                        <form action="registroPreguntas.php" method="post" id="formPreguntas">
                            <input type="hidden" name="operation" id="operation" value="add">
                            <input type="hidden" name="pregunta-id" id="pregunta-id" value="">
                            
                            <!-- Campo para seleccionar pregunta (solo en editar/eliminar) -->
                            <div class="form-group" id="grupo-seleccionar-pregunta" style="display: none;">
                                <label for="">Seleccionar Pregunta</label>
                                <select name="pregunta_seleccionada" id="select-pregunta" class="form-control">
                                    <option value="">Selecciona una pregunta</option>
                                    <?php
                                    // Cargar preguntas para selección
                                    try {
                                        $queryPreguntas = "SELECT p.id_pregunta, p.texto_pregunta, c.nombre_categoria, t.nombre_tema 
                                                          FROM preguntas p 
                                                          LEFT JOIN categoria c ON p.cod_categoria = c.id_categoria
                                                          LEFT JOIN temas t ON p.id_tema = t.id_tema
                                                          ORDER BY p.id_pregunta DESC";
                                        $stmtPreguntas = $pdo->prepare($queryPreguntas);
                                        $stmtPreguntas->execute();
                                        $preguntasLista = $stmtPreguntas->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        foreach ($preguntasLista as $preguntaItem) {
                                            $textoCorto = strlen($preguntaItem['texto_pregunta']) > 50 
                                                ? substr($preguntaItem['texto_pregunta'], 0, 50) . '...' 
                                                : $preguntaItem['texto_pregunta'];
                                            echo "<option value='" . $preguntaItem['id_pregunta'] . "'>";
                                            echo "ID:" . $preguntaItem['id_pregunta'] . " - " . htmlspecialchars($textoCorto);
                                            echo " (" . $preguntaItem['nombre_categoria'] . " - " . $preguntaItem['nombre_tema'] . ")";
                                            echo "</option>";
                                        }
                                    } catch (Exception $e) {
                                        echo "<option value=''>Error al cargar preguntas</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group" id="grupo-pregunta" style="display: block;">
                                <label for="">Pregunta</label>
                                <textarea name="pregunta" id="input-pregunta" placeholder="Escribe tu pregunta aquí..." required class="form-control" rows="3"></textarea>
                            </div>

                            <div class="form-group" id="grupo-categoria" style="display: block;">
                                <label for="">Categoría</label>
                                <select name="categoria" id="input-categoria" required class="form-control">
                                    <option value="">Selecciona una categoría</option>
                                    <option value="3">Principiante</option>
                                    <option value="2">Novato</option>
                                    <option value="1">Experto</option>
                                </select>
                            </div>

                            <div class="form-group" id="grupo-tema" style="display: block;">
                                <label for="">Tema</label>
                                <select name="tema" id="input-tema" required class="form-control">
                                    <option value="">Selecciona un tema</option>
                                    <?php
                                    // Cargar temas desde la base de datos
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

                            <div class="form-group" id="grupo-puntos" style="display: block;">
                                <label for="">Puntos</label>
                                <input type="number" name="puntos" id="input-puntos" value="10" readonly class="form-control">
                                <small class="form-text text-muted">Los puntos son fijos en 10 para todas las preguntas</small>
                            </div>

                            <div class="form-group" id="grupo-respuesta-correcta" style="display: block;">
                                <label for="">Respuesta Correcta</label>
                                <input type="text" name="respuesta_correcta" id="input-respuesta-correcta" placeholder="Escribe la respuesta correcta" required class="form-control">
                            </div>

                            <div class="form-group" id="grupo-respuesta-incorrecta-1" style="display: block;">
                                <label for="">Respuesta Incorrecta 1</label>
                                <input type="text" name="respuesta_incorrecta_1" id="input-respuesta-incorrecta-1" placeholder="Primera respuesta incorrecta" required class="form-control">
                            </div>

                            <div class="form-group" id="grupo-respuesta-incorrecta-2" style="display: block;">
                                <label for="">Respuesta Incorrecta 2 (Opcional)</label>
                                <input type="text" name="respuesta_incorrecta_2" id="input-respuesta-incorrecta-2" placeholder="Segunda respuesta incorrecta" class="form-control">
                                <small class="form-text text-muted">Para preguntas de verdadero/falso, deja este campo vacío</small>
                            </div>

                            <div class="form-group" id="grupo-respuesta-incorrecta-3" style="display: block;">
                                <label for="">Respuesta Incorrecta 3 (Opcional)</label>
                                <input type="text" name="respuesta_incorrecta_3" id="input-respuesta-incorrecta-3" placeholder="Tercera respuesta incorrecta" class="form-control">
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
                        <?php include 'cargarPreguntas.php'; ?>
                    </tbody>
                </table>
            </div>
        </div>
            </div>
        </main>
    </div>

    <!-- Scripts necesarios -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/preguntasManager.js"></script>

    <!-- Script para manejo dinámico del formulario -->
    <script>
        console.log('Script cargado correctamente');
        
        // Variables globales para el estado del formulario
        let currentOperation = 'add';
        let selectedQuestionId = null;

        // Función para cambiar la operación del formulario
        function changeOperation(operation) {
            console.log('Cambiando operación a:', operation);
            currentOperation = operation;
            
            // Actualizar botones activos
            document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
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
            toggleFormFields(operation);
            
            // Actualizar el texto del botón principal
            updateSubmitButton(operation);
            
            // Limpiar formulario si es operación de añadir
            if (operation === 'add') {
                clearForm();
            }
        }

        // Función para mostrar/ocultar campos del formulario
        function toggleFormFields(operation) {
            console.log('Configurando campos para operación:', operation);
            const fieldsToHide = ['grupo-categoria', 'grupo-tema', 'grupo-pregunta', 'grupo-respuesta-correcta', 
                                 'grupo-respuesta-incorrecta-1', 'grupo-respuesta-incorrecta-2', 'grupo-respuesta-incorrecta-3'];
            
            if (operation === 'add') {
                // Mostrar todos los campos para añadir
                fieldsToHide.forEach(field => {
                    const element = document.getElementById(field);
                    if (element) {
                        element.style.display = 'block';
                        console.log('Mostrando campo:', field);
                    } else {
                        console.error('No se encontró el campo:', field);
                    }
                });
                
                const selectorGroup = document.getElementById('grupo-seleccionar-pregunta');
                if (selectorGroup) {
                    selectorGroup.style.display = 'none';
                    console.log('Ocultando selector de pregunta');
                } else {
                    console.error('No se encontró grupo-seleccionar-pregunta');
                }
                
                // Hacer campos requeridos
                const preguntaField = document.getElementById('input-pregunta');
                const categoriaField = document.getElementById('input-categoria');
                const temaField = document.getElementById('input-tema');
                
                if (preguntaField) preguntaField.required = true;
                if (categoriaField) categoriaField.required = true;
                if (temaField) temaField.required = true;
                
            } else if (operation === 'edit') {
                // Mostrar selector de pregunta y todos los campos de edición
                const selectorGroup = document.getElementById('grupo-seleccionar-pregunta');
                if (selectorGroup) {
                    selectorGroup.style.display = 'block';
                    console.log('Mostrando selector de pregunta');
                }
                
                fieldsToHide.forEach(field => {
                    const element = document.getElementById(field);
                    if (element) {
                        element.style.display = 'block';
                        console.log('Mostrando campo:', field);
                    }
                });
                
                // Hacer campos requeridos
                const preguntaField = document.getElementById('input-pregunta');
                const categoriaField = document.getElementById('input-categoria');
                const temaField = document.getElementById('input-tema');
                
                if (preguntaField) preguntaField.required = true;
                if (categoriaField) categoriaField.required = true;
                if (temaField) temaField.required = true;
                
            } else if (operation === 'delete') {
                // Solo mostrar selector de pregunta
                const selectorGroup = document.getElementById('grupo-seleccionar-pregunta');
                if (selectorGroup) {
                    selectorGroup.style.display = 'block';
                    console.log('Mostrando selector de pregunta para eliminar');
                }
                
                fieldsToHide.forEach(field => {
                    const element = document.getElementById(field);
                    if (element) {
                        element.style.display = 'none';
                        console.log('Ocultando campo:', field);
                    }
                });
                
                // No hacer campos requeridos para eliminar
                const preguntaField = document.getElementById('input-pregunta');
                const categoriaField = document.getElementById('input-categoria');
                const temaField = document.getElementById('input-tema');
                
                if (preguntaField) preguntaField.required = false;
                if (categoriaField) categoriaField.required = false;
                if (temaField) temaField.required = false;
            }
        }

        // Función para actualizar el botón de envío
        function updateSubmitButton(operation) {
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