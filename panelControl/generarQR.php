<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

// Incluir la librería de QR Code (usando una API gratuita)
function generarCodigoQR($url, $size = '200x200') {
    // Usar la API de QR Server (gratuita y confiable)
    $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size={$size}&data=" . urlencode($url);
    return $qr_url;
}

// Obtener la URL completa para la descarga
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script_path = dirname($_SERVER['REQUEST_URI']);

// URL para descargar las estadísticas
$download_url = $protocol . '://' . $host . $script_path . '/descargarEstadisticas.php';

// Generar el código QR
$qr_image_url = generarCodigoQR($download_url, '300x300');

// Si se solicita solo la imagen del QR (para AJAX)
if (isset($_GET['solo_imagen']) && $_GET['solo_imagen'] === '1') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'qr_url' => $qr_image_url,
        'download_url' => $download_url
    ]);
    exit();
}

// Obtener información del usuario
$usuario_nombre = $_SESSION['usuario_nombre'] ?? 'Usuario';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código QR - Descarga Móvil</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #533483 0%, #0f3460 100%);
            min-height: 100vh;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .contenedor-qr {
            background: rgba(255, 255, 255, 0.95);
            color: #333;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 90%;
            backdrop-filter: blur(10px);
        }

        .titulo-qr {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #533483;
        }

        .subtitulo-qr {
            font-size: 16px;
            margin-bottom: 30px;
            color: #666;
            line-height: 1.5;
        }

        .codigo-qr {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: 3px solid #533483;
        }

        .codigo-qr img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .instrucciones {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 5px solid #28a745;
            text-align: left;
        }

        .instrucciones h4 {
            color: #28a745;
            margin-top: 0;
            font-size: 18px;
        }

        .instrucciones ol {
            margin: 10px 0;
            padding-left: 20px;
        }

        .instrucciones li {
            margin: 8px 0;
            line-height: 1.4;
        }

        .url-directa {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border: 1px solid #dee2e6;
            word-break: break-all;
            font-family: monospace;
            font-size: 12px;
            color: #495057;
        }

        .botones-accion {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .boton {
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .boton-primario {
            background: linear-gradient(135deg, #533483, #7b2cbf);
            color: white;
            box-shadow: 0 4px 15px rgba(83, 52, 131, 0.3);
        }

        .boton-primario:hover {
            background: linear-gradient(135deg, #7b2cbf, #533483);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(83, 52, 131, 0.4);
        }

        .boton-secundario {
            background: #6c757d;
            color: white;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }

        .boton-secundario:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
        }

        .icono-dispositivo {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .info-adicional {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }

        .info-adicional strong {
            color: #533483;
        }

        @media (max-width: 600px) {
            .contenedor-qr {
                padding: 20px;
                margin: 10px;
            }
            
            .titulo-qr {
                font-size: 24px;
            }
            
            .botones-accion {
                flex-direction: column;
                align-items: center;
            }
            
            .boton {
                width: 100%;
                max-width: 200px;
            }
        }

        /* Animación de carga */
        .loading-qr {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner-qr {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #533483;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="contenedor-qr">
        <div class="icono-dispositivo">📱</div>
        <h1 class="titulo-qr">Descarga Móvil</h1>
        <p class="subtitulo-qr">
            Escanea el código QR con tu dispositivo móvil para descargar tus estadísticas de trivia en formato Excel
        </p>

        <div class="loading-qr" id="loadingQR">
            <div class="spinner-qr"></div>
            <p>Generando código QR...</p>
        </div>

        <div class="codigo-qr" id="codigoQR">
            <img src="<?php echo $qr_image_url; ?>" alt="Código QR para descarga" id="imagenQR">
        </div>

        <div class="instrucciones">
            <h4>📋 Instrucciones:</h4>
            <ol>
                <li><strong>Abre la cámara</strong> de tu teléfono o una app de QR</li>
                <li><strong>Apunta al código QR</strong> que aparece arriba</li>
                <li><strong>Toca la notificación</strong> que aparece en tu pantalla</li>
                <li><strong>El archivo Excel</strong> se descargará automáticamente</li>
                <li><strong>Abre con tu app</strong> favorita (Excel, Google Sheets, etc.)</li>
            </ol>
        </div>

        <div class="info-adicional">
            <strong>💡 Tip:</strong> Si el código QR no funciona, puedes usar el enlace directo que aparece abajo o enviártelo por WhatsApp/email.
        </div>

        <div class="url-directa">
            <strong>URL directa:</strong><br>
            <span id="urlDirecta"><?php echo $download_url; ?></span>
        </div>

        <div class="botones-accion">
            <a href="<?php echo $download_url; ?>" class="boton boton-primario" target="_blank">
                📥 Descargar Ahora
            </a>
            
            <button class="boton boton-secundario" onclick="copiarURL()">
                📋 Copiar URL
            </button>
            
            <button class="boton boton-secundario" onclick="compartirWhatsApp()">
                📱 WhatsApp
            </button>
            
            <a href="inicio.php" class="boton boton-secundario">
                ← Regresar
            </a>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee; color: #666; font-size: 14px;">
            <strong>Usuario:</strong> <?php echo htmlspecialchars($usuario_nombre); ?><br>
            <strong>Generado:</strong> <?php echo date('d/m/Y H:i:s'); ?>
        </div>
    </div>

    <script>
        // Función para copiar URL al portapapeles
        function copiarURL() {
            const url = document.getElementById('urlDirecta').textContent;
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(function() {
                    alert('✅ URL copiada al portapapeles');
                }, function(err) {
                    console.error('Error al copiar: ', err);
                    copiarURLFallback(url);
                });
            } else {
                copiarURLFallback(url);
            }
        }

        // Función fallback para copiar URL
        function copiarURLFallback(url) {
            const textArea = document.createElement('textarea');
            textArea.value = url;
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    alert('✅ URL copiada al portapapeles');
                } else {
                    alert('❌ No se pudo copiar la URL. Cópiala manualmente.');
                }
            } catch (err) {
                console.error('Error al copiar: ', err);
                alert('❌ No se pudo copiar la URL. Cópiala manualmente.');
            }
            
            document.body.removeChild(textArea);
        }

        // Función para compartir por WhatsApp
        function compartirWhatsApp() {
            const url = document.getElementById('urlDirecta').textContent;
            const mensaje = `🧠 ReichMind - Mis Estadísticas de Trivia\n\n📊 Descarga mis resultados en Excel:\n${url}\n\n¡Mira qué tal lo estoy haciendo! 🎯`;
            const whatsappURL = `https://wa.me/?text=${encodeURIComponent(mensaje)}`;
            
            window.open(whatsappURL, '_blank');
        }

        // Regenerar QR si es necesario
        function regenerarQR() {
            const loadingDiv = document.getElementById('loadingQR');
            const codigoDiv = document.getElementById('codigoQR');
            const imagenQR = document.getElementById('imagenQR');
            
            loadingDiv.style.display = 'block';
            codigoDiv.style.display = 'none';
            
            fetch('generarQR.php?solo_imagen=1')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        imagenQR.src = data.qr_url;
                        document.getElementById('urlDirecta').textContent = data.download_url;
                        
                        loadingDiv.style.display = 'none';
                        codigoDiv.style.display = 'block';
                    } else {
                        alert('Error al generar el código QR');
                        loadingDiv.style.display = 'none';
                        codigoDiv.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de conexión al generar QR');
                    loadingDiv.style.display = 'none';
                    codigoDiv.style.display = 'block';
                });
        }

        // Verificar si la imagen del QR se carga correctamente
        document.getElementById('imagenQR').onerror = function() {
            console.log('Error al cargar imagen QR, intentando regenerar...');
            regenerarQR();
        };

        console.log('✅ Página de código QR cargada correctamente');
    </script>
</body>
</html>
