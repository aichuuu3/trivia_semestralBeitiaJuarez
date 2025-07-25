<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba - Botón Trivia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f0f0f0;
        }
        .boton-test {
            background: #007bff;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
        }
        .boton-test:hover {
            background: #0056b3;
        }
        .debug {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <h1>🧪 Prueba de Funcionalidad - Botón Trivia</h1>
    
    <div>
        <button class="boton-test" onclick="testBasico()">
            ✅ Test Básico - Alert
        </button>
        
        <button class="boton-test" onclick="testCargaCategorias()">
            📂 Test Cargar Categorías
        </button>
        
        <button class="boton-test" onclick="testDOMManipulation()">
            🔧 Test DOM Manipulation
        </button>
    </div>
    
    <div class="debug" id="debug-output">
        <h3>📋 Debug Output:</h3>
        <p>Haz clic en cualquier botón para ver el resultado...</p>
    </div>
    
    <div id="test-container" style="margin-top: 20px; padding: 20px; background: white; border-radius: 8px;">
        <h3>🎯 Test Container</h3>
        <p>Este contenedor se usará para las pruebas de DOM</p>
    </div>

    <script>
        function log(mensaje) {
            const output = document.getElementById('debug-output');
            const tiempo = new Date().toLocaleTimeString();
            output.innerHTML += `<p><strong>[${tiempo}]</strong> ${mensaje}</p>`;
            console.log(mensaje);
        }
        
        function testBasico() {
            log('🟢 Test básico ejecutado correctamente');
            alert('✅ JavaScript está funcionando correctamente!');
        }
        
        function testCargaCategorias() {
            log('📡 Iniciando test de carga de categorías...');
            
            fetch('cargarCategorias.php')
                .then(response => {
                    log(`📥 Respuesta recibida: Status ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    log(`📊 Datos recibidos: ${JSON.stringify(data, null, 2)}`);
                    if (data.status === 'success') {
                        log(`✅ Categorías cargadas: ${data.data.length} encontradas`);
                    } else {
                        log(`❌ Error: ${data.message}`);
                    }
                })
                .catch(error => {
                    log(`❌ Error de conexión: ${error.message}`);
                });
        }
        
        function testDOMManipulation() {
            log('🔧 Iniciando test de manipulación DOM...');
            
            try {
                const container = document.getElementById('test-container');
                
                // Crear elemento de prueba
                const testDiv = document.createElement('div');
                testDiv.innerHTML = `
                    <h4>🧪 Elemento de Prueba</h4>
                    <p>Creado dinámicamente a las ${new Date().toLocaleTimeString()}</p>
                    <button onclick="this.parentElement.remove()">🗑️ Eliminar</button>
                `;
                testDiv.style.cssText = `
                    background: #e7f3ff;
                    padding: 15px;
                    border-radius: 8px;
                    border: 2px solid #007bff;
                    margin-top: 10px;
                `;
                
                container.appendChild(testDiv);
                log('✅ Elemento creado y añadido al DOM correctamente');
                
            } catch (error) {
                log(`❌ Error en manipulación DOM: ${error.message}`);
            }
        }
        
        // Log inicial
        log('🚀 Página de prueba cargada correctamente');
    </script>
</body>
</html>
