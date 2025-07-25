// Archivo para manejar las operaciones AJAX del módulo de preguntas

// Función para enviar datos del formulario
function submitForm() {
    const formData = new FormData(document.getElementById('formPreguntas'));
    
    // Validaciones según el tipo de operación
    const operation = document.getElementById('operation').value;
    
    if (operation === 'delete') {
        if (!document.getElementById('pregunta-id').value) {
            Swal.fire('Error', 'Selecciona una pregunta para eliminar', 'error');
            return;
        }
        
        // Confirmar eliminación
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                executeAjaxRequest(formData);
            }
        });
    } else {
        executeAjaxRequest(formData);
    }
}

// Función para ejecutar la petición AJAX
function executeAjaxRequest(formData) {
    const xhr = new XMLHttpRequest();
    
    xhr.open('POST', 'registroPreguntas.php', true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    handleResponse(response);
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    Swal.fire('Error', 'Error en la respuesta del servidor', 'error');
                }
            } else {
                Swal.fire('Error', 'Error de conexión con el servidor', 'error');
            }
        }
    };
    
    xhr.send(formData);
}

// Función para manejar la respuesta del servidor
function handleResponse(response) {
    if (response.success) {
        const operation = document.getElementById('operation').value;
        const messages = {
            'add': 'Pregunta añadida correctamente',
            'edit': 'Pregunta actualizada correctamente',
            'delete': 'Pregunta eliminada correctamente'
        };
        
        Swal.fire('Éxito', messages[operation], 'success').then(() => {
            // Limpiar formulario y recargar tabla
            clearForm();
            reloadTable();
            
            // Volver al modo añadir después de una operación exitosa
            if (operation !== 'add') {
                changeOperation('add');
            }
        });
    } else {
        Swal.fire('Error', response.message || 'Error al procesar la solicitud', 'error');
    }
}

// Función para recargar la tabla de preguntas
function reloadTable() {
    // Hacer una petición para obtener las preguntas actualizadas
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'cargarPreguntas.php', true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById('resultado').innerHTML = xhr.responseText;
            // También actualizar el select de preguntas
            updateQuestionSelect();
        }
    };
    
    xhr.send();
}

// Función para cargar datos de una pregunta específica
function loadQuestionDetails(questionId) {
    if (!questionId) return;
    
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `cargarPregunta.php?id=${questionId}`, true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                const data = JSON.parse(xhr.responseText);
                if (data.success) {
                    populateForm(data.pregunta);
                }
            } catch (e) {
                console.error('Error al cargar datos de pregunta:', e);
            }
        }
    };
    
    xhr.send();
}

// Función para llenar el formulario con datos de una pregunta
function populateForm(pregunta) {
    document.getElementById('input-pregunta').value = pregunta.texto_pregunta || '';
    document.getElementById('input-categoria').value = pregunta.cod_categoria || '';
    document.getElementById('input-tema').value = pregunta.id_tema || '';
    
    // Cargar respuestas
    if (pregunta.respuestas) {
        const correcta = pregunta.respuestas.find(r => r.es_correcta == 1);
        const incorrectas = pregunta.respuestas.filter(r => r.es_correcta == 0);
        
        if (correcta) {
            document.getElementById('input-respuesta-correcta').value = correcta.texto_respuesta;
        }
        
        incorrectas.forEach((resp, index) => {
            const fieldId = `input-respuesta-incorrecta-${index + 1}`;
            const field = document.getElementById(fieldId);
            if (field) {
                field.value = resp.texto_respuesta;
            }
        });
    }
}

// Función para actualizar el select de preguntas
function updateQuestionSelect() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'cargarListaPreguntas.php', true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById('select-pregunta').innerHTML = xhr.responseText;
        }
    };
    
    xhr.send();
}

// Función para implementar búsqueda en tiempo real
function setupSearch() {
    const searchInput = document.getElementById('buscar');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchQuestions(this.value);
        }, 300);
    });
}

// Función para buscar preguntas
function searchQuestions(searchTerm) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `buscarPreguntas.php?q=${encodeURIComponent(searchTerm)}`, true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById('resultado').innerHTML = xhr.responseText;
        }
    };
    
    xhr.send();
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Configurar el botón de registro
    document.getElementById('registrar').addEventListener('click', submitForm);
    
    // Configurar búsqueda
    setupSearch();
    
    // Cargar lista inicial de preguntas para el select
    updateQuestionSelect();
});
