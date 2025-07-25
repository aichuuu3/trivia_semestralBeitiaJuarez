// Script para gestión de usuarios
document.addEventListener('DOMContentLoaded', function() {
    cargarUsuarios();
    
    // Validación en tiempo real de contraseñas
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    confirmPasswordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        const confirmPassword = this.value;
        
        if (confirmPassword && password !== confirmPassword) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        } else if (confirmPassword && password === confirmPassword) {
            this.classList.add('is-valid');
            this.classList.remove('is-invalid');
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
    });
});

// Función para mostrar/ocultar contraseña
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId === 'password' ? 'togglePasswordIcon' : 'toggleConfirmPasswordIcon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Función para cargar usuarios desde la base de datos
function cargarUsuarios() {
    fetch('obtenerUsuarios.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                mostrarUsuarios(data.data);
            } else {
                console.error('Error al cargar usuarios:', data.message);
                document.getElementById('resultado').innerHTML = 
                    '<tr><td colspan="7" class="text-center text-danger">Error al cargar usuarios</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error de conexión:', error);
            document.getElementById('resultado').innerHTML = 
                '<tr><td colspan="7" class="text-center text-danger">Error de conexión</td></tr>';
        });
}

// Función para mostrar usuarios en la tabla
function mostrarUsuarios(usuarios) {
    const tbody = document.getElementById('resultado');
    
    if (usuarios.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay usuarios registrados</td></tr>';
        return;
    }
    
    let html = '';
    usuarios.forEach(usuario => {
        // Mostrar solo una parte del hash de la contraseña por seguridad
        const passwordMostrar = usuario.password ? usuario.password.substring(0, 15) + '...' : 'Sin contraseña';
        
        // Determinar el nivel basado en la categoría
        const nivel = usuario.nombre_categoria || 'Sin asignar';
        
        // Formatear fecha de registro
        const fechaRegistro = new Date(usuario.fecha_registro).toLocaleDateString('es-ES');
        
        html += `
            <tr>
                <td><strong>${usuario.id}</strong></td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="ml-2">
                            <div style="font-weight: 500;">${usuario.nombre}</div>
                            <small class="text-muted">${fechaRegistro}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <a href="mailto:${usuario.email}" class="text-primary">
                        ${usuario.email}
                    </a>
                </td>
                <td>
                    <span class="text-muted font-monospace" style="font-size: 0.8em;">
                        ${passwordMostrar}
                    </span>
                </td>
                <td>
                    <span class="badge badge-success px-2 py-1">
                        💰 ${usuario.monedas_totales}
                    </span>
                </td>
                <td>
                    <span class="badge ${getBadgeClass(nivel)} px-2 py-1">
                        ${getIconoNivel(nivel)} ${nivel}
                    </span>
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="editarUsuario(${usuario.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarUsuario(${usuario.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="verDetalles(${usuario.id})" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Función para obtener clase CSS del badge según el nivel
function getBadgeClass(nivel) {
    switch (nivel.toLowerCase()) {
        case 'principiante':
            return 'badge-success';
        case 'novato':
            return 'badge-warning';
        case 'experto':
            return 'badge-danger';
        default:
            return 'badge-secondary';
    }
}

// Función para obtener icono según el nivel
function getIconoNivel(nivel) {
    switch (nivel.toLowerCase()) {
        case 'principiante':
            return '🌱';
        case 'novato':
            return '⚡';
        case 'experto':
            return '🔥';
        default:
            return '❓';
    }
}

// Función para buscar usuarios
document.getElementById('buscar').addEventListener('input', function() {
    const termino = this.value.toLowerCase();
    const filas = document.querySelectorAll('#resultado tr');
    
    filas.forEach(fila => {
        const texto = fila.textContent.toLowerCase();
        if (texto.includes(termino)) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
});

// Función para editar usuario
function editarUsuario(id) {
    // Obtener datos del usuario actual
    fetch(`obtenerUsuario.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const usuario = data.data;
                
                Swal.fire({
                    title: 'Editar Usuario',
                    html: `
                        <div class="text-left">
                            <div class="form-group">
                                <label>Nombre:</label>
                                <input type="text" id="edit_nombre" class="form-control" value="${usuario.nombre}">
                            </div>
                            <div class="form-group">
                                <label>Email:</label>
                                <input type="email" id="edit_email" class="form-control" value="${usuario.email}">
                            </div>
                            <div class="form-group">
                                <label>Monedas:</label>
                                <input type="number" id="edit_monedas" class="form-control" value="${usuario.monedas_totales}">
                            </div>
                            <div class="form-group">
                                <label>Nivel de Conocimiento:</label>
                                <select id="edit_categoria" class="form-control">
                                    <option value="3" ${usuario.cod_categoria == 3 ? 'selected' : ''}>🌱 Principiante</option>
                                    <option value="2" ${usuario.cod_categoria == 2 ? 'selected' : ''}>⚡ Novato</option>
                                    <option value="1" ${usuario.cod_categoria == 1 ? 'selected' : ''}>🔥 Experto</option>
                                </select>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Actualizar',
                    cancelButtonText: 'Cancelar',
                    preConfirm: () => {
                        const nombre = document.getElementById('edit_nombre').value;
                        const email = document.getElementById('edit_email').value;
                        const monedas = document.getElementById('edit_monedas').value;
                        const categoria = document.getElementById('edit_categoria').value;
                        
                        if (!nombre || !email || !monedas || !categoria) {
                            Swal.showValidationMessage('Todos los campos son obligatorios');
                            return false;
                        }
                        
                        return { nombre, email, monedas, categoria };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        actualizarUsuario(id, result.value);
                    }
                });
            }
        })
        .catch(() => {
            Swal.fire('Error', 'No se pudo cargar la información del usuario', 'error');
        });
}

// Función para actualizar usuario
function actualizarUsuario(id, datos) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('nombre', datos.nombre);
    formData.append('email', datos.email);
    formData.append('monedas', datos.monedas);
    formData.append('categoria', datos.categoria);
    
    fetch('actualizarUsuario.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire('Éxito', 'Usuario actualizado correctamente', 'success');
            cargarUsuarios(); // Recargar tabla
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(() => {
        Swal.fire('Error', 'Error de conexión', 'error');
    });
}

// Función para eliminar usuario
function eliminarUsuario(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción eliminará permanentemente el usuario',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Enviar petición de eliminación
            fetch('eliminarUsuario.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Eliminado', 'El usuario ha sido eliminado exitosamente', 'success');
                    cargarUsuarios(); // Recargar tabla
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Error de conexión', 'error');
            });
        }
    });
}

// Función para ver detalles del usuario
function verDetalles(id) {
    fetch(`obtenerUsuario.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const usuario = data.data;
                const fechaRegistro = new Date(usuario.fecha_registro).toLocaleString('es-ES');
                
                Swal.fire({
                    title: `👤 Detalles de ${usuario.nombre}`,
                    html: `
                        <div class="text-left">
                            <p><strong>🆔 ID:</strong> ${usuario.id}</p>
                            <p><strong>📧 Email:</strong> ${usuario.email}</p>
                            <p><strong>💰 Monedas:</strong> ${usuario.monedas_totales}</p>
                            <p><strong>📊 Nivel:</strong> ${usuario.nombre_categoria || 'Sin asignar'}</p>
                            <p><strong>📅 Registro:</strong> ${fechaRegistro}</p>
                            <p><strong>✅ Estado:</strong> ${usuario.activo ? 'Activo' : 'Inactivo'}</p>
                        </div>
                    `,
                    width: 450,
                    confirmButtonText: 'Cerrar'
                });
            }
        })
        .catch(() => {
            Swal.fire('Error', 'No se pudo cargar la información del usuario', 'error');
        });
}

// Función para registrar nuevo usuario
document.getElementById('registrar').addEventListener('click', function(e) {
    e.preventDefault();
    
    const form = document.getElementById('frm');
    const formData = new FormData(form);
    
    // Validar que las contraseñas coincidan
    const password = formData.get('password');
    const confirmPassword = formData.get('confirm_password');
    
    if (password !== confirmPassword) {
        Swal.fire({
            title: 'Error',
            text: 'Las contraseñas no coinciden',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Validar longitud de contraseña
    if (password.length < 6) {
        Swal.fire({
            title: 'Error',
            text: 'La contraseña debe tener al menos 6 caracteres',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Mostrar loading
    Swal.fire({
        title: 'Registrando usuario...',
        text: 'Por favor espere',
        icon: 'info',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Enviar datos al servidor
    fetch('registrarUsuario.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        
        if (data.status === 'success') {
            Swal.fire({
                title: '¡Éxito!',
                html: `
                    <div class="text-left">
                        <p><strong>Usuario registrado exitosamente:</strong></p>
                        <ul>
                            <li><strong>Nombre:</strong> ${data.data.nombre}</li>
                            <li><strong>Email:</strong> ${data.data.email}</li>
                            <li><strong>Monedas iniciales:</strong> 💰 ${data.data.monedas_iniciales}</li>
                        </ul>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                form.reset();
                cargarUsuarios(); // Recargar la tabla
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.close();
        Swal.fire({
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
});
