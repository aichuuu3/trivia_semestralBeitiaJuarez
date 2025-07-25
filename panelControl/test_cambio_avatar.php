<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Cambio de Avatar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        .galeria-test {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .avatar-test {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            border: 3px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.7);
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h2>🧪 Test de Cambio de Avatar</h2>
        
        <h3>📁 Imágenes disponibles en la base de datos:</h3>
        <div id="galeria-imagenes" class="galeria-test">
            <p>Cargando imágenes...</p>
        </div>
        
        <h3>📊 Información técnica:</h3>
        <div id="info-tecnica">
            <p>Verificando conexión...</p>
        </div>
        
        <h3>🔧 Pruebas de funcionalidad:</h3>
        <button onclick="probarCargarImagenes()" style="padding: 10px 20px; margin: 5px; background: #533483; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Cargar Imágenes
        </button>
        
        <div id="resultados-prueba" style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <h4>Resultados de las pruebas:</h4>
            <div id="log-pruebas"></div>
        </div>
    </div>

    <script>
        function log(mensaje) {
            const logDiv = document.getElementById('log-pruebas');
            logDiv.innerHTML += `<p>${new Date().toLocaleTimeString()}: ${mensaje}</p>`;
        }

        function probarCargarImagenes() {
            log('🔄 Iniciando carga de imágenes...');
            
            fetch('cargarImagenes.php')
                .then(response => {
                    log(`📡 Respuesta recibida: ${response.status} ${response.statusText}`);
                    return response.json();
                })
                .then(data => {
                    log(`📋 Datos recibidos: ${JSON.stringify(data)}`);
                    
                    const galeriaDiv = document.getElementById('galeria-imagenes');
                    const infoDiv = document.getElementById('info-tecnica');
                    
                    if (data.status === 'success') {
                        log(`✅ ${data.data.length} imágenes cargadas correctamente`);
                        
                        let galeriaHTML = '';
                        data.data.forEach((imagen, index) => {
                            galeriaHTML += `
                                <div class="avatar-test" style="background-image: url('../img/${imagen.nombre_archivo}')" title="${imagen.nombre_display}">
                                    ${index + 1}
                                </div>
                            `;
                        });
                        galeriaDiv.innerHTML = galeriaHTML;
                        
                        infoDiv.innerHTML = `
                            <ul>
                                <li><strong>Estado:</strong> ✅ Conexión exitosa</li>
                                <li><strong>Imágenes encontradas:</strong> ${data.data.length}</li>
                                <li><strong>Endpoint:</strong> cargarImagenes.php funcionando</li>
                            </ul>
                        `;
                        
                    } else {
                        log(`❌ Error: ${data.message}`);
                        galeriaDiv.innerHTML = `<p style="color: red;">❌ ${data.message}</p>`;
                        infoDiv.innerHTML = `<p style="color: red;">❌ Error de conexión o consulta</p>`;
                    }
                })
                .catch(error => {
                    log(`❌ Error de conexión: ${error.message}`);
                    console.error('Error:', error);
                    document.getElementById('galeria-imagenes').innerHTML = `<p style="color: red;">❌ Error de conexión</p>`;
                    document.getElementById('info-tecnica').innerHTML = `<p style="color: red;">❌ Error: ${error.message}</p>`;
                });
        }

        // Cargar automáticamente al iniciar
        document.addEventListener('DOMContentLoaded', probarCargarImagenes);
    </script>
</body>
</html>
