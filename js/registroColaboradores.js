// JavaScript para gestión de colaboradores xd

// cargar colaboradores al iniciar xd
document.addEventListener('DOMContentLoaded', function() {
    cargarColaboradores();
    
    // event listeners para los botones xd
    document.getElementById('registrar').addEventListener('click', guardarColaborador);
    document.getElementById('cancelar').addEventListener('click', cancelarEdicion);
    document.getElementById('buscar').addEventListener('input', function() {
        cargarColaboradores(this.value);
    });
    
    // script para el menú lateral xd
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.getAttribute('href') === '#') {
                e.preventDefault();
            }
            
            document.querySelectorAll('.sidebar-menu a').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            const section = this.querySelector('span:last-child').textContent;
            console.log(`Navegando a: ${section}`);
        });
    });
});

function cargarColaboradores(busqueda = '') {
    fetch('listarColaboradores.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: busqueda
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('resultado').innerHTML = data;
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('resultado').innerHTML = 
            '<tr><td colspan="6" class="text-center text-danger">Error al cargar colaboradores</td></tr>';
    });
}

function guardarColaborador() {
    // validación básica antes de enviar xd
    const nombre = document.getElementById('nombre').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const isEdit = document.getElementById('colaboradorId').value !== '';

    if (!nombre || !email || (!isEdit && !password)) {
        Swal.fire('Error', 'Por favor complete todos los campos obligatorios', 'error');
        return;
    }

    if (!isEdit && password.length < 6) {
        Swal.fire('Error', 'La contraseña debe tener al menos 6 caracteres', 'error');
        return;
    }

    // crear FormData manualmente porque así es la vida xd
    const formData = new FormData();
    formData.append('nombre', nombre);
    formData.append('email', email);
    formData.append('password', password);
    if (isEdit) {
        formData.append('id', document.getElementById('colaboradorId').value);
    }

    fetch('registroColaboradores.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data === 'ok') {
            Swal.fire('Éxito', 'Colaborador registrado correctamente', 'success');
            limpiarFormulario();
            cargarColaboradores();
        } else if (data === 'modificado') {
            Swal.fire('Éxito', 'Colaborador actualizado correctamente', 'success');
            limpiarFormulario();
            cargarColaboradores();
        } else {
            try {
                const jsonData = JSON.parse(data);
                if (jsonData.errors) {
                    Swal.fire('Error', 'Errores encontrados:\n' + jsonData.errors.join('\n'), 'error');
                } else {
                    Swal.fire('Error', jsonData.message || 'Error desconocido', 'error');
                }
            } catch (e) {
                Swal.fire('Error', data, 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Error al procesar la solicitud', 'error');
    });
}

function Editar(id) {
    // obtener datos del colaborador para editar xd
    const formData = new FormData();
    formData.append('operacion', 'obtener');
    formData.append('id', id);
    
    fetch('registroColaboradores.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            Swal.fire('Error', data.error, 'error');
        } else {
            document.getElementById('colaboradorId').value = data.id;
            document.getElementById('nombre').value = data.nombre;
            document.getElementById('email').value = data.email;
            document.getElementById('password').value = '';
            document.getElementById('password').required = false;
            document.getElementById('formTitle').textContent = 'Editar Colaborador';
            document.getElementById('registrar').value = 'Actualizar';
            document.getElementById('cancelar').style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Error al cargar los datos del colaborador', 'error');
    });
}

function Eliminar(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: 'Esta acción desactivará al colaborador',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // eliminando al colaborador xd
            const formData = new FormData();
            formData.append('operacion', 'eliminar');
            formData.append('id', id);
            
            fetch('registroColaboradores.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'eliminado') {
                    Swal.fire('Eliminado', 'Colaborador eliminado correctamente', 'success');
                    cargarColaboradores();
                } else {
                    Swal.fire('Error', data, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Error al eliminar el colaborador', 'error');
            });
        }
    });
}

function cancelarEdicion() {
    // cancelar edición y limpiar todo xd
    limpiarFormulario();
}

function limpiarFormulario() {
    // limpiar el form para empezar de nuevo xd
    document.getElementById('frm').reset();
    document.getElementById('colaboradorId').value = '';
    document.getElementById('password').required = true;
    document.getElementById('formTitle').textContent = 'Registro de Colaboradores';
    document.getElementById('registrar').value = 'Registrar';
    document.getElementById('cancelar').style.display = 'none';
}
