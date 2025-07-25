<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test - Diagnóstico WAMP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #533483 0%, #0f3460 100%);
            color: white;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .test-container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 30px;
            backdrop-filter: blur(10px);
        }
        .test-item {
            margin: 15px 0;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }
        .success { border-left: 5px solid #28a745; }
        .error { border-left: 5px solid #dc3545; }
        .info { border-left: 5px solid #17a2b8; }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🔧 Diagnóstico del Sistema</h1>
        
        <div class="test-item info">
            <h3>📍 Información del Servidor</h3>
            <p><strong>Fecha/Hora:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><strong>Servidor:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'No disponible'; ?></p>
            <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
            <p><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'No disponible'; ?></p>
        </div>

        <div class="test-item info">
            <h3>📂 Rutas de Archivos</h3>
            <p><strong>Archivo actual:</strong> <?php echo __FILE__; ?></p>
            <p><strong>Directorio actual:</strong> <?php echo __DIR__; ?></p>
            <p><strong>URL solicitada:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? 'No disponible'; ?></p>
        </div>

        <div class="test-item <?php echo file_exists('../css/inicio.css') ? 'success' : 'error'; ?>">
            <h3>🎨 CSS</h3>
            <p>Archivo inicio.css: <?php echo file_exists('../css/inicio.css') ? '✅ Encontrado' : '❌ No encontrado'; ?></p>
            <p>Ruta: <?php echo realpath('../css/inicio.css') ?: 'No existe'; ?></p>
        </div>

        <div class="test-item <?php echo file_exists('../js/ventanas.js') ? 'success' : 'error'; ?>">
            <h3>📜 JavaScript</h3>
            <p>Archivo ventanas.js: <?php echo file_exists('../js/ventanas.js') ? '✅ Encontrado' : '❌ No encontrado'; ?></p>
            <p>Ruta: <?php echo realpath('../js/ventanas.js') ?: 'No existe'; ?></p>
        </div>

        <div class="test-item <?php echo file_exists('../bd/conexion.php') ? 'success' : 'error'; ?>">
            <h3>🗄️ Base de Datos</h3>
            <p>Archivo conexion.php: <?php echo file_exists('../bd/conexion.php') ? '✅ Encontrado' : '❌ No encontrado'; ?></p>
            <?php if (file_exists('../bd/conexion.php')): ?>
                <?php
                try {
                    require_once '../bd/conexion.php';
                    echo '<p>✅ Conexión a BD: Exitosa</p>';
                } catch (Exception $e) {
                    echo '<p>❌ Error de conexión: ' . $e->getMessage() . '</p>';
                }
                ?>
            <?php endif; ?>
        </div>

        <div class="test-item info">
            <h3>🔗 Enlaces de Prueba</h3>
            <p><a href="inicio.php" style="color: #ffd700;">🏠 Ir a inicio.php</a></p>
            <p><a href="cargarCategorias.php" style="color: #ffd700;">📋 Probar cargarCategorias.php</a></p>
            <p><a href="../css/inicio.css" style="color: #ffd700;">🎨 Ver CSS directamente</a></p>
            <p><a href="../js/ventanas.js" style="color: #ffd700;">📜 Ver JavaScript directamente</a></p>
        </div>

        <div class="test-item info">
            <h3>🌐 Instrucciones</h3>
            <p>1. Verifica que WAMP esté ejecutándose (ícono verde)</p>
            <p>2. Asegúrate de acceder via: <code>http://localhost/trivia_semestralBeitiaJuarez/panelControl/inicio.php</code></p>
            <p>3. Limpia el caché del navegador (Ctrl+F5)</p>
            <p>4. Verifica la consola del navegador (F12) para errores JavaScript</p>
        </div>
    </div>
</body>
</html>
