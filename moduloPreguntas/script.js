$(document).ready(function() {
    // Llamar AJAX al hacer clic en registrar
    $("#registrar").click(function(e) {
        e.preventDefault();
        
        // Obtener datos del formulario
        let datos = new FormData(document.getElementById("frm"));
        
        // Validar que los campos requeridos estén llenos
        if (!datos.get('pregunta') || !datos.get('categoria') || !datos.get('tema') || !datos.get('respuesta_correcta') || !datos.get('respuesta_incorrecta_1')) {
            Swal.fire('Error', 'Por favor completa todos los campos requeridos', 'error');
            return;
        }

        $.ajax({
            url: "registroPreguntas.php",
            type: "POST",
            data: datos,
            processData: false,
            contentType: false,
            success: function(r) {
                console.log("Respuesta del servidor:", r);
                try {
                    if (r && r.trim() !== '') {
                        let response = JSON.parse(r);
                        if (response.status === 'success') {
                            Swal.fire('Éxito', response.message, 'success').then(() => {
                                // Limpiar formulario
                                document.getElementById("frm").reset();
                                // Recargar tabla
                                cargarTabla();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    } else {
                        Swal.fire('Error', 'Respuesta vacía del servidor', 'error');
                    }
                } catch (e) {
                    console.error("Error al parsear JSON:", e);
                    console.log("Respuesta cruda:", r);
                    Swal.fire('Error', 'Error en la respuesta del servidor: ' + r, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error AJAX:", {xhr, status, error});
                Swal.fire('Error', 'Error de conexión: ' + error, 'error');
            }
        });
    });

    // Función para cargar la tabla
    function cargarTabla() {
        $.post("cargarPreguntas.php", function(data) {
            $("#resultado").html(data);
        }).fail(function() {
            console.error("Error al cargar la tabla");
        });
    }

    // Búsqueda en tiempo real
    $("#buscar").keyup(function() {
        let textoBusqueda = $(this).val();
        if (textoBusqueda.length > 0) {
            $.post("buscarPreguntas.php", {busqueda: textoBusqueda}, function(data) {
                $("#resultado").html(data);
            });
        } else {
            cargarTabla();
        }
    });
});
