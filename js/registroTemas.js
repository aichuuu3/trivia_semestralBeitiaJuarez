
// JavaScript para gestión de temas
document.addEventListener('DOMContentLoaded', function() {
    cargarTemas();
    document.getElementById('registrar').addEventListener('click', guardarTema);
    document.getElementById('buscar').addEventListener('input', function() {
        cargarTemas(this.value);
    });
});

function cargarTemas(busqueda = '') {
    fetch('listarTemas.php', {
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
            '<tr><td colspan="3" class="text-center text-danger">Error al cargar temas</td></tr>';
    });
}

function guardarTema() {
    const nombre_tema = document.getElementById('nombre_tema').value.trim();
    const descripcion = document.getElementById('descripcion').value.trim();
    const id_tema = document.getElementById('id_tema') ? document.getElementById('id_tema').value : '';
    const isEdit = id_tema !== '';

    if (!nombre_tema || !descripcion) {
        Swal.fire('Error', 'Por favor complete todos los campos obligatorios', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('nombre_tema', nombre_tema);
    formData.append('descripcion', descripcion);
    formData.append('operacion', isEdit ? 'actualizar' : 'crear');
    if (isEdit) {
        formData.append('id_tema', id_tema);
    }

    fetch('registroTema.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data === 'ok') {
            Swal.fire('Éxito', 'Tema registrado correctamente', 'success');
            limpiarFormulario();
            cargarTemas();
        } else if (data === 'modificado') {
            Swal.fire('Éxito', 'Tema actualizado correctamente', 'success');
            limpiarFormulario();
            cargarTemas();
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

// Si quieres agregar editar/eliminar, aquí puedes agregar funciones similares a guardarTema

function limpiarFormulario() {
    document.getElementById('frm').reset();
    if (document.getElementById('id_tema')) document.getElementById('id_tema').value = '';
    document.getElementById('registrar').value = 'Registrar';
}
