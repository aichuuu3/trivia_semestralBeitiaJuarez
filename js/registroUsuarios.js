
// JavaScript para gestión de usuarios

document.addEventListener('DOMContentLoaded', function() {
    cargarUsuarios();
    document.getElementById('registrar').addEventListener('click', guardarUsuario);
    document.getElementById('cancelar').addEventListener('click', cancelarEdicion);
    document.getElementById('buscar').addEventListener('input', function() {
        cargarUsuarios(this.value);
    });
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

function cargarUsuarios(busqueda = '') {
    fetch('listarUsuario.php', {
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
            '<tr><td colspan="6" class="text-center text-danger">Error al cargar usuarios</td></tr>';
    });
}

function guardarUsuario() {
    const nombre = document.getElementById('nombre').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const isEdit = document.getElementById('usuarioId') && document.getElementById('usuarioId').value !== '';

    if (!nombre || !email || (!isEdit && !password)) {
        Swal.fire('Error', 'Por favor complete todos los campos obligatorios', 'error');
        return;
    }
    if (!isEdit && password.length < 6) {
        Swal.fire('Error', 'La contraseña debe tener al menos 6 caracteres', 'error');
        return;
    }
    const formData = new FormData();
    formData.append('nombre', nombre);
    formData.append('email', email);
    formData.append('password', password);
    if (isEdit) {
        formData.append('id', document.getElementById('usuarioId').value);
    }
    fetch('registroUsuario.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data === 'ok') {
            Swal.fire('Éxito', 'Usuario registrado correctamente', 'success');
            limpiarFormulario();
            cargarUsuarios();
        } else if (data === 'modificado') {
            Swal.fire('Éxito', 'Usuario actualizado correctamente', 'success');
            limpiarFormulario();
            cargarUsuarios();
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
    const formData = new FormData();
    formData.append('operacion', 'obtener');
    formData.append('id', id);
    fetch('registroUsuario.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            Swal.fire('Error', data.error, 'error');
        } else {
            document.getElementById('usuarioId').value = data.id;
            document.getElementById('nombre').value = data.nombre;
            document.getElementById('email').value = data.email;
            document.getElementById('password').value = '';
            document.getElementById('password').required = false;
            document.getElementById('formTitle').textContent = 'Editar Usuario';
            document.getElementById('registrar').value = 'Actualizar';
            document.getElementById('cancelar').style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Error al cargar los datos del usuario', 'error');
    });
}

function Eliminar(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: 'Esta acción desactivará al usuario',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('operacion', 'eliminar');
            formData.append('id', id);
            fetch('registroUsuario.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'eliminado') {
                    Swal.fire('Eliminado', 'Usuario eliminado correctamente', 'success');
                    cargarUsuarios();
                } else {
                    Swal.fire('Error', data, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Error al eliminar el usuario', 'error');
            });
        }
    });
}

function cancelarEdicion() {
    limpiarFormulario();
}

function limpiarFormulario() {
    document.getElementById('frm').reset();
    if (document.getElementById('usuarioId')) document.getElementById('usuarioId').value = '';
    document.getElementById('password').required = true;
    document.getElementById('formTitle').textContent = 'Registro de Usuarios';
    document.getElementById('registrar').value = 'Registrar';
    document.getElementById('cancelar').style.display = 'none';
}
