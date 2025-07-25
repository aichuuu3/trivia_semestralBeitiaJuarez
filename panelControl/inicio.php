<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

// Incluir conexión a la base de datos
require_once '../bd/conexion.php';

// Obtener información del usuario desde la sesión
$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['usuario_nombre'] ?? 'Usuario';
$usuario_nivel = $_SESSION['usuario_nivel'] ?? 'Sin asignar';
$usuario_monedas = $_SESSION['usuario_monedas'] ?? 0;
$usuario_email = $_SESSION['usuario_email'] ?? '';

// Obtener el avatar del usuario desde la base de datos
$usuario_avatar = 'avatar.png'; // Valor por defecto
try {
    $stmt = $pdo->prepare("SELECT avatar, cod_categoria FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($resultado && !empty($resultado['avatar'])) {
        $usuario_avatar = $resultado['avatar'];
    }
    
    // Obtener el nivel actual del usuario desde la base de datos
    if ($resultado && $resultado['cod_categoria']) {
        $stmtNivel = $pdo->prepare("SELECT nombre_categoria FROM categoria WHERE id_categoria = ?");
        $stmtNivel->execute([$resultado['cod_categoria']]);
        $nivelResult = $stmtNivel->fetch(PDO::FETCH_ASSOC);
        if ($nivelResult) {
            $usuario_nivel = $nivelResult['nombre_categoria'];
            $_SESSION['usuario_nivel'] = $usuario_nivel; // Actualizar sesión
        }
    }
} catch (PDOException $e) {
    // En caso de error, usar valores por defecto
    $usuario_avatar = 'avatar.png';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Dashboard</title>
    <!-- Forzar recarga de CSS -->
    <link rel="stylesheet" href="../css/inicio.css?v=<?php echo time(); ?>">
    <script src="../js/ventanas.js?v=<?php echo time(); ?>"></script>
    <!-- Meta tags para evitar caché -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- Estilos críticos inline para asegurar visualización -->
    <style>
        /* Estilos críticos inline */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #533483 0%, #0f3460 100%);
            min-height: 100vh;
            color: white;
        }
        
        /* Spinner básico */
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #7b2cbf;
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
        
        /* Estilos básicos para botones */
        button {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        button:hover {
            transform: translateY(-2px);
        }
        
        /* Estilos para el avatar clickeable */
        .circulo-avatar:hover .avatar-imagen {
            border-color: rgba(255,255,255,0.5) !important;
            box-shadow: 0 6px 20px rgba(0,0,0,0.3) !important;
        }
        
        .circulo-avatar:hover .icono-editar-avatar {
            opacity: 1 !important;
        }
        
        /* Estilos del modal de avatar */
        .modal-avatar {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideIn {
            from { 
                opacity: 0;
                transform: translateY(-30px) scale(0.9);
            }
            to { 
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .modal-contenido {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            color: #333;
            max-height: 80vh;
            overflow-y: auto;
            animation: slideIn 0.3s ease;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }
        
        .modal-title {
            font-size: 24px;
            font-weight: bold;
            color: #533483;
        }
        
        .cerrar-modal {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .cerrar-modal:hover {
            color: #533483;
            transform: none;
        }
        
        .seccion-modal {
            margin-bottom: 25px;
        }
        
        .titulo-seccion {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #533483;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .galeria-avatares {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .avatar-opcion {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            border: 3px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .avatar-opcion:hover {
            border-color: #533483;
            transform: scale(1.1);
        }
        
        .avatar-opcion.seleccionado {
            border-color: #533483;
            box-shadow: 0 0 15px rgba(83, 52, 131, 0.5);
        }
        
        .avatar-opcion::after {
            content: '✓';
            position: absolute;
            bottom: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            background: #533483;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .avatar-opcion.seleccionado::after {
            opacity: 1;
        }
        
        .upload-area {
            border: 2px dashed #533483;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .upload-area:hover {
            border-color: #7b2cbf;
            background: #f0f2ff;
        }
        
        .upload-area.dragover {
            border-color: #7b2cbf;
            background: #e8eaff;
        }
        
        .upload-icon {
            font-size: 48px;
            margin-bottom: 10px;
            color: #533483;
        }
        
        .botones-modal {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .boton-modal {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .boton-cancelar {
            background: #6c757d;
            color: white;
        }
        
        .boton-cancelar:hover {
            background: #5a6268;
        }
        
        .boton-guardar {
            background: #533483;
            color: white;
        }
        
        .boton-guardar:hover {
            background: #7b2cbf;
        }
        
        .boton-guardar:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        /* Estilos para el modal de bloqueo */
        .modal-bloqueo {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }
        
        .modal-bloqueo-contenido {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            margin: 10% auto;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            color: #333;
            text-align: center;
            animation: slideIn 0.3s ease;
            border: 2px solid #e9ecef;
        }
        
        .icono-bloqueo {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.8;
        }
        
        .titulo-bloqueo {
            color: #dc3545;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .mensaje-bloqueo {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #495057;
        }
        
        .progreso-monedas {
            margin: 20px 0;
        }
        
        .barra-progreso-contenedor {
            background: #e9ecef;
            border-radius: 25px;
            overflow: hidden;
            height: 40px;
            position: relative;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .barra-progreso-monedas {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            border-radius: 25px;
            transition: width 0.6s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }
        
        .texto-progreso {
            color: white;
            font-weight: bold;
            font-size: 14px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
        
        .boton-cerrar-bloqueo {
            background: linear-gradient(135deg, #533483, #7b2cbf);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(83, 52, 131, 0.3);
        }
        
        .boton-cerrar-bloqueo:hover {
            background: linear-gradient(135deg, #7b2cbf, #533483);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(83, 52, 131, 0.4);
        }
        
        /* Estilos para categorías bloqueadas */
        .boton-categoria-bloqueada {
            background: linear-gradient(135deg, #e9ecef, #dee2e6) !important;
            color: #6c757d !important;
            cursor: not-allowed !important;
            opacity: 0.7;
            border: 2px dashed #adb5bd !important;
        }
        
        .boton-categoria-bloqueada:hover {
            transform: none !important;
            box-shadow: none !important;
        }
        
        .monedas-faltantes {
            background: #fff3cd;
            color: #856404;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 8px;
            border: 1px solid #ffeaa7;
        }
    </style>
</head>
<body>
    <!-- Modal de cambio de avatar -->
    <div id="modalAvatar" class="modal-avatar">
        <div class="modal-contenido">
            <div class="modal-header">
                <h2 class="modal-title">🖼️ Cambiar Avatar</h2>
                <button class="cerrar-modal" onclick="cerrarModalAvatar()">&times;</button>
            </div>
            
            <div class="seccion-modal">
                <h3 class="titulo-seccion">
                    📁 Seleccionar de la galería
                </h3>
                <div class="galeria-avatares" id="galeriaAvatares">
                    <!-- Se cargará dinámicamente -->
                </div>
            </div>
            
            <div class="seccion-modal">
                <h3 class="titulo-seccion">
                    📤 Subir imagen personalizada
                </h3>
                <div class="upload-area" 
                     onclick="document.getElementById('fileInput').click()"
                     ondrop="manejarDrop(event)" 
                     ondragover="manejarDragOver(event)"
                     ondragenter="manejarDragEnter(event)"
                     ondragleave="manejarDragLeave(event)">
                    <div class="upload-icon">📷</div>
                    <p><strong>Haz clic aquí o arrastra una imagen</strong></p>
                    <p>Formatos soportados: JPG, PNG, GIF (máx. 5MB)</p>
                    <input type="file" id="fileInput" accept="image/*" style="display: none;" onchange="manejarArchivoSeleccionado(event)">
                </div>
                <div id="vistaPrevia" style="display: none; margin-top: 15px;">
                    <p><strong>Vista previa:</strong></p>
                    <img id="imagenPrevia" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #533483;">
                </div>
            </div>
            
            <div class="botones-modal">
                <button class="boton-modal boton-cancelar" onclick="cerrarModalAvatar()">
                    Cancelar
                </button>
                <button class="boton-modal boton-guardar" id="botonGuardar" onclick="guardarAvatar()" disabled>
                    Guardar Cambios
                </button>
            </div>
        </div>
    </div>
    
    <!-- Modal de categoría bloqueada -->
    <div id="modalBloqueo" class="modal-bloqueo">
        <div class="modal-bloqueo-contenido">
            <div class="icono-bloqueo">🔒</div>
            <h2 class="titulo-bloqueo" id="tituloBloqueo">Categoría Bloqueada</h2>
            <p class="mensaje-bloqueo" id="mensajeBloqueo">
                <!-- Se llenará dinámicamente -->
            </p>
            
            <div class="progreso-monedas">
                <div class="barra-progreso-contenedor">
                    <div class="barra-progreso-monedas" id="barraProgresoMonedas" style="width: 0%;">
                        <div class="texto-progreso" id="textoProgreso">0 / 0</div>
                    </div>
                </div>
            </div>
            
            <div style="margin: 15px 0; padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #533483;">
                <strong>💡 Consejo:</strong> Juega partidas en la categoría <strong>Principiante</strong> para ganar más monedas y desbloquear nuevas categorías.
            </div>
            
            <button class="boton-cerrar-bloqueo" onclick="cerrarModalBloqueo()">
                Entendido
            </button>
        </div>
    </div>
    
    <!-- Navbar -->
    <nav class="barra-navegacion">
        <div class="contenedor-nav">
            <div class="marca-nav">
                <h1>🧠 ReichMind</h1>
            </div>
            
            <div class="menu-nav">
                <a href="#" class="enlace-nav activo">🏠 Inicio</a>
            </div>
            
            <div class="usuario-nav">
                <button class="boton-cerrar-sesion" onclick="cerrarSesion()">
                    Cerrar Sesión
                </button>
            </div>
        </div>
    </nav>

    <div class="contenedor-dashboard">
        <!-- Contenedor 1: Perfil de Usuario -->
        <div class="contenedor perfil-contenedor">
            <div class="cabecera-perfil">
                <div class="contenedor-avatar">
                    <div class="circulo-avatar" onclick="abrirModalAvatar()" style="cursor: pointer;" title="Haz clic para cambiar tu avatar">
                        <?php
                        // Ruta a la carpeta de imágenes
                        $ruta_avatar = "../img/" . $usuario_avatar;
                        
                        // Debug: mostrar información en comentarios HTML
                        echo "<!-- Debug Avatar: Usuario ID: $usuario_id, Avatar: $usuario_avatar, Ruta: $ruta_avatar, Existe: " . (file_exists($ruta_avatar) ? 'SI' : 'NO') . " -->";
                        
                        // Verificar si el archivo de avatar existe
                        if (file_exists($ruta_avatar)) {
                            // Mostrar imagen del avatar desde la base de datos
                            ?>
                            <div class="avatar-imagen" style="
                                width: 100%;
                                height: 100%;
                                border-radius: 50%;
                                background-image: url('<?php echo $ruta_avatar; ?>');
                                background-size: cover;
                                background-position: center;
                                background-repeat: no-repeat;
                                position: relative;
                                overflow: hidden;
                                border: 3px solid rgba(255,255,255,0.2);
                                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                                transition: all 0.3s ease;
                            ">
                                <!-- Efecto de overlay sutil -->
                                <div style="
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                    width: 100%;
                                    height: 100%;
                                    background: linear-gradient(135deg, rgba(0,0,0,0.1), transparent);
                                    border-radius: 50%;
                                "></div>
                                
                                <!-- Icono de edición que aparece al hover -->
                                <div class="icono-editar-avatar" style="
                                    position: absolute;
                                    bottom: 5px;
                                    right: 5px;
                                    width: 25px;
                                    height: 25px;
                                    background: rgba(0,0,0,0.7);
                                    border-radius: 50%;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    color: white;
                                    font-size: 12px;
                                    opacity: 0;
                                    transition: opacity 0.3s ease;
                                ">
                                    ✏️
                                </div>
                            </div>
                            <?php
                        } else {
                            // Fallback: mostrar iniciales si no existe la imagen
                            $iniciales = '';
                            $palabras = explode(' ', $usuario_nombre);
                            foreach($palabras as $palabra) {
                                if (!empty($palabra)) {
                                    $iniciales .= strtoupper(substr($palabra, 0, 1));
                                }
                            }
                            $iniciales = substr($iniciales, 0, 2); // Máximo 2 iniciales
                            
                            // Colores de fondo basados en la primera letra
                            $colores = [
                                'A' => '#FF6B6B', 'B' => '#4ECDC4', 'C' => '#45B7D1', 'D' => '#96CEB4',
                                'E' => '#FECA57', 'F' => '#FF9FF3', 'G' => '#54A0FF', 'H' => '#5F27CD',
                                'I' => '#00D2D3', 'J' => '#FF9F43', 'K' => '#10AC84', 'L' => '#EE5A24',
                                'M' => '#0984E3', 'N' => '#6C5CE7', 'O' => '#A29BFE', 'P' => '#FD79A8',
                                'Q' => '#E17055', 'R' => '#00B894', 'S' => '#00CEC9', 'T' => '#6C5CE7',
                                'U' => '#A29BFE', 'V' => '#FD79A8', 'W' => '#FDCB6E', 'X' => '#E84393',
                                'Y' => '#2D3436', 'Z' => '#636E72'
                            ];
                            
                            $primera_letra = substr($iniciales, 0, 1);
                            $color_fondo = isset($colores[$primera_letra]) ? $colores[$primera_letra] : '#74B9FF';
                            ?>
                            
                            <!-- Avatar con iniciales (fallback) -->
                            <div class="avatar-iniciales" style="
                                background: linear-gradient(135deg, <?php echo $color_fondo; ?>, <?php echo $color_fondo; ?>cc);
                                width: 100%;
                                height: 100%;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: white;
                                font-weight: bold;
                                font-size: 24px;
                                text-shadow: 0 1px 3px rgba(0,0,0,0.3);
                                position: relative;
                                overflow: hidden;
                            ">
                                <!-- Efecto de brillo -->
                                <div style="
                                    position: absolute;
                                    top: 10%;
                                    left: 10%;
                                    width: 30%;
                                    height: 30%;
                                    background: rgba(255,255,255,0.3);
                                    border-radius: 50%;
                                    filter: blur(8px);
                                "></div>
                                
                                <!-- Iniciales del usuario -->
                                <span style="position: relative; z-index: 2;">
                                    <?php echo $iniciales; ?>
                                </span>
                            </div>
                            <?php
                        }
                        ?>
                        
                        <!-- Indicador de nivel en la esquina -->
                        <div class="indicador-nivel" style="
                            position: absolute;
                            bottom: 0;
                            right: 0;
                            width: 24px;
                            height: 24px;
                            border-radius: 50%;
                            background: <?php 
                                switch($usuario_nivel) {
                                    case 'Principiante': echo '#4CAF50'; break;
                                    case 'Novato': echo '#FF9800'; break;
                                    case 'Experto': echo '#F44336'; break;
                                    default: echo '#9E9E9E';
                                }
                            ?>;
                            border: 3px solid white;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 12px;
                            font-weight: bold;
                            color: white;
                            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                        ">
                            <?php 
                                switch($usuario_nivel) {
                                    case 'Principiante': echo '🌱'; break;
                                    case 'Novato': echo '⚡'; break;
                                    case 'Experto': echo '🔥'; break;
                                    default: echo '❓';
                                }
                            ?>
                        </div>
                    </div>
                    <div class="barra-nivel">
                        <div class="progreso-nivel" style="--progress: <?php 
                            // Calcular progreso basado en monedas
                            $progreso = min(($usuario_monedas / 1000) * 100, 100);
                            echo $progreso;
                        ?>%"></div>
                    </div>
                </div>
                
                <div class="info-usuario">
                    <h2 class="nombre-usuario" id="username-display"><?php echo htmlspecialchars($usuario_nombre); ?></h2>
                    <p class="nivel-usuario">Nivel: <?php echo htmlspecialchars($usuario_nivel); ?></p>
                </div>
            </div>
            
            <div class="cuadricula-estadisticas">
                <div class="tarjeta-estadistica">
                    <div class="icono-estadistica">💰</div>
                    <div class="numero-estadistica"><?php echo number_format($usuario_monedas); ?></div>
                    <div class="etiqueta-estadistica">Monedas Totales</div>
                </div>
                
                <div class="tarjeta-estadistica">
                    <div class="icono-estadistica">🏆</div>
                    <div class="numero-estadistica">--</div>
                    <div class="etiqueta-estadistica">Partidas Ganadas</div>
                </div>
                
                <div class="tarjeta-estadistica">
                    <div class="icono-estadistica">❌</div>
                    <div class="numero-estadistica">--</div>
                    <div class="etiqueta-estadistica">Partidas Fallidas</div>
                </div>
                
                <div class="tarjeta-estadistica">
                    <div class="icono-estadistica">⭐</div>
                    <div class="numero-estadistica"><?php 
                        // Mostrar número del nivel basado en la categoría
                        $nivel_numero = '';
                        switch($usuario_nivel) {
                            case 'Principiante':
                                $nivel_numero = '1';
                                break;
                            case 'Novato':
                                $nivel_numero = '2';
                                break;
                            case 'Experto':
                                $nivel_numero = '3';
                                break;
                            default:
                                $nivel_numero = '-';
                        }
                        echo $nivel_numero;
                    ?></div>
                    <div class="etiqueta-estadistica">Nivel</div>
                </div>
            </div>
        </div>
        
        <div class="contenedor contenedor-secundario">
            <h2 class="titulo-formulario">Panel de Juego</h2>
            
            <!-- boton para iniciar partida -->
            <div class="seccion-partida">
                <button class="boton-iniciar-partida" onclick="iniciarNuevaPartida()">
                    Iniciar Nueva Partida
                </button>
                
                <!-- Botón de debug temporal -->
                <button style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; margin-left: 10px; cursor: pointer;" onclick="alert('✅ JavaScript funciona!')">
                    🔧 Test
                </button>
            </div>

            <!-- listas de estadisticas -->
            <div class="contenedor-listas">
                <div class="lista-contenedor">
                    <h3 class="titulo-lista">💰 Monedas Ganadas</h3>
                    <ul class="lista-monedas" id="lista-monedas">
                        <li class="lista-vacia">No hay registros aún</li>
                    </ul>
                </div>

                <div class="lista-contenedor">
                    <h3 class="titulo-lista">⏱️ Puntos y Tiempo</h3>
                    <ul class="lista-puntos-tiempo" id="lista-puntos-tiempo">
                        <li class="lista-vacia">No hay registros aún</li>
                    </ul>
                </div>
            </div>

            <!-- boton de descarga -->
            <div class="seccion-descarga">
                <button class="boton-descargar" onclick="descargarEstadisticas()">
                    Descargar Estadísticas
                </button>
            </div>
        </div>
    </div>

    <script>
        console.log('🚀 Inicializando inicio.php...');
        
        // Esperar a que la página se cargue completamente
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📄 DOM cargado completamente');
            console.log('👤 Usuario logueado: <?php echo addslashes($usuario_nombre); ?>');
            console.log('🎯 Nivel: <?php echo addslashes($usuario_nivel); ?>');
            console.log('💰 Monedas: <?php echo $usuario_monedas; ?>');
            
            // Verificar si las funciones de ventanas.js están disponibles
            if (typeof verificarSesion === 'function') {
                // No es necesario verificar sesión, ya se carga desde PHP
                // verificarSesion();
                console.log('✅ Sesión verificada desde PHP');
            } else {
                console.warn('⚠️ verificarSesion no está disponible');
            }
            
            if (typeof limpiarEstadoSesion === 'function') {
                limpiarEstadoSesion();
            } else {
                console.warn('⚠️ limpiarEstadoSesion no está disponible');
            }
        });

        // funcion para iniciar nueva partida
        function iniciarNuevaPartida() {
            console.log('🎮 Iniciando nueva partida...');
            
            // Mostrar información sobre el estado de las categorías
            const monedasUsuario = <?php echo $usuario_monedas; ?>;
            let mensajeInfo = '';
            
            if (monedasUsuario < 150) {
                mensajeInfo = `💰 Tienes ${monedasUsuario} monedas. Necesitas 150 para desbloquear Novato y 200 para Experto.`;
            } else if (monedasUsuario < 200) {
                mensajeInfo = `💰 Tienes ${monedasUsuario} monedas. ¡Solo te faltan ${200 - monedasUsuario} monedas para desbloquear Experto!`;
            } else {
                mensajeInfo = `💰 ¡Excelente! Tienes ${monedasUsuario} monedas. Todas las categorías están desbloqueadas.`;
            }
            
            console.log(mensajeInfo);
            
            try {
                // Ocultar el contenido actual del panel de juego
                const seccionPartida = document.querySelector('.seccion-partida');
                const contenedorListas = document.querySelector('.contenedor-listas');
                const seccionDescarga = document.querySelector('.seccion-descarga');
                
                console.log('📋 Elementos encontrados:', {
                    seccionPartida: !!seccionPartida,
                    contenedorListas: !!contenedorListas,
                    seccionDescarga: !!seccionDescarga
                });
                
                if (seccionPartida) seccionPartida.style.display = 'none';
                if (contenedorListas) contenedorListas.style.display = 'none';
                if (seccionDescarga) seccionDescarga.style.display = 'none';
                
                // Crear contenedor para selección de categorías
                const contenedorCategorias = document.createElement('div');
                contenedorCategorias.className = 'contenedor-categorias';
                contenedorCategorias.innerHTML = `
                    <h3 class="titulo-categorias">🎯 Selecciona una Categoría</h3>
                    <div class="info-monedas" style="
                        background: linear-gradient(135deg, #e3f2fd, #bbdefb);
                        border: 2px solid #2196f3;
                        border-radius: 10px;
                        padding: 15px;
                        margin-bottom: 20px;
                        text-align: center;
                        color: #0d47a1;
                        font-weight: bold;
                    ">
                        ${mensajeInfo}
                    </div>
                    <div class="loading-categorias">
                        <div class="spinner"></div>
                        <p>Cargando categorías...</p>
                    </div>
                    <div class="botones-categorias" style="display: none;"></div>
                    <button class="boton-regresar" onclick="regresarPanelPrincipal()">
                        ← Regresar
                    </button>
                `;
                
                // Insertar después del título del formulario
                const tituloFormulario = document.querySelector('.titulo-formulario');
                if (tituloFormulario) {
                    tituloFormulario.insertAdjacentElement('afterend', contenedorCategorias);
                    console.log('✅ Contenedor de categorías insertado');
                } else {
                    console.error('❌ No se encontró el título del formulario');
                    return;
                }
                
                // Cargar categorías desde el servidor
                cargarCategorias();
                
            } catch (error) {
                console.error('❌ Error en iniciarNuevaPartida:', error);
                alert('Error al iniciar nueva partida: ' + error.message);
            }
        }

        // funcion para cargar categorias desde el servidor
        function cargarCategorias() {
            fetch('cargarCategorias.php')
                .then(response => response.json())
                .then(data => {
                    const loadingDiv = document.querySelector('.loading-categorias');
                    const botonesDiv = document.querySelector('.botones-categorias');
                    
                    if (data.status === 'success') {
                        // Ocultar loading
                        loadingDiv.style.display = 'none';
                        
                        // Obtener monedas del usuario desde PHP
                        const monedasUsuario = <?php echo $usuario_monedas; ?>;
                        console.log('💰 Monedas del usuario:', monedasUsuario);
                        
                        // Crear botones de categorías con verificación de bloqueo
                        let botonesHTML = '';
                        data.data.forEach(categoria => {
                            const iconos = {
                                'Principiante': '🌱',
                                'Novato': '⚡',
                                'Experto': '🔥'
                            };
                            
                            const colores = {
                                'Principiante': 'verde',
                                'Novato': 'amarillo', 
                                'Experto': 'rojo'
                            };
                            
                            // Verificar si la categoría está bloqueada
                            let bloqueada = false;
                            let razonBloqueo = '';
                            let monedasRequeridas = 0;
                            
                            if (categoria.nombre_categoria === 'Novato' && monedasUsuario < 150) {
                                bloqueada = true;
                                razonBloqueo = 'Necesitas 150 monedas';
                                monedasRequeridas = 150;
                            } else if (categoria.nombre_categoria === 'Experto' && monedasUsuario < 200) {
                                bloqueada = true;
                                razonBloqueo = 'Necesitas 200 monedas';
                                monedasRequeridas = 200;
                            }
                            
                            // Crear el botón con estilos condicionales
                            const claseBoton = bloqueada ? 'boton-categoria-bloqueada' : `boton-categoria ${colores[categoria.nombre_categoria]}`;
                            const funcionClick = bloqueada ? 
                                `mostrarMensajeBloqueo('${categoria.nombre_categoria}', ${monedasRequeridas}, ${monedasUsuario})` : 
                                `seleccionarCategoria(${categoria.id_categoria}, '${categoria.nombre_categoria}')`;
                            
                            const descripcion = bloqueada ? razonBloqueo : 
                                              (categoria.nombre_categoria === 'Principiante' ? 'Preguntas básicas' : 
                                               categoria.nombre_categoria === 'Novato' ? 'Preguntas intermedias' : 
                                               'Preguntas avanzadas');
                            
                            botonesHTML += `
                                <button class="${claseBoton}" 
                                        onclick="${funcionClick}"
                                        ${bloqueada ? 'disabled' : ''}
                                        title="${bloqueada ? razonBloqueo : 'Clic para seleccionar'}">
                                    <span class="icono-categoria">${iconos[categoria.nombre_categoria]}${bloqueada ? '🔒' : ''}</span>
                                    <span class="nombre-categoria">${categoria.nombre_categoria}</span>
                                    <span class="descripcion-categoria">${descripcion}</span>
                                    ${bloqueada ? 
                                        `<div class="monedas-faltantes">
                                            ${monedasRequeridas - monedasUsuario} monedas más
                                        </div>` : ''}
                                </button>
                            `;
                        });
                        
                        botonesDiv.innerHTML = botonesHTML;
                        botonesDiv.style.display = 'grid';
                        
                    } else {
                        loadingDiv.innerHTML = `
                            <div class="error-loading">
                                <p>❌ Error al cargar categorías</p>
                                <p>${data.message}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const loadingDiv = document.querySelector('.loading-categorias');
                    loadingDiv.innerHTML = `
                        <div class="error-loading">
                            <p>❌ Error de conexión</p>
                            <p>No se pudieron cargar las categorías</p>
                        </div>
                    `;
                });
        }

        // funcion para regresar al panel principal
        function regresarPanelPrincipal() {
            console.log('Regresando al panel principal...');
            
            // Eliminar contenedor de categorías
            const contenedorCategorias = document.querySelector('.contenedor-categorias');
            if (contenedorCategorias) {
                contenedorCategorias.remove();
            }
            
            // Mostrar elementos originales
            const seccionPartida = document.querySelector('.seccion-partida');
            const contenedorListas = document.querySelector('.contenedor-listas');
            const seccionDescarga = document.querySelector('.seccion-descarga');
            
            seccionPartida.style.display = 'block';
            contenedorListas.style.display = 'flex';
            seccionDescarga.style.display = 'block';
        }

        // Función para mostrar mensaje de categoría bloqueada
        function mostrarMensajeBloqueo(categoria, monedasRequeridas, monedasActuales) {
            console.log(`🔒 Mostrando bloqueo para ${categoria}: ${monedasActuales}/${monedasRequeridas} monedas`);
            
            const modal = document.getElementById('modalBloqueo');
            const titulo = document.getElementById('tituloBloqueo');
            const mensaje = document.getElementById('mensajeBloqueo');
            const barraProgreso = document.getElementById('barraProgresoMonedas');
            const textoProgreso = document.getElementById('textoProgreso');
            
            // Configurar contenido del modal
            titulo.textContent = `Categoría ${categoria} Bloqueada`;
            
            const monedasFaltantes = monedasRequeridas - monedasActuales;
            mensaje.innerHTML = `
                Para acceder a la categoría <strong>${categoria}</strong> necesitas <strong>${monedasRequeridas} monedas</strong>.<br>
                Actualmente tienes <strong>${monedasActuales} monedas</strong>.<br>
                Te faltan <strong style="color: #dc3545;">${monedasFaltantes} monedas</strong> para desbloquear esta categoría.
            `;
            
            // Configurar barra de progreso
            const porcentaje = Math.min((monedasActuales / monedasRequeridas) * 100, 100);
            barraProgreso.style.width = porcentaje + '%';
            textoProgreso.textContent = `${monedasActuales} / ${monedasRequeridas}`;
            
            // Mostrar modal
            modal.style.display = 'block';
        }

        // Función para cerrar modal de bloqueo
        function cerrarModalBloqueo() {
            const modal = document.getElementById('modalBloqueo');
            modal.style.display = 'none';
        }

        // funcion para seleccionar categoria
        function seleccionarCategoria(idCategoria, nombreCategoria) {
            console.log(`Categoría seleccionada: ${nombreCategoria} (ID: ${idCategoria})`);
            
            // Guardar la categoría actual para verificación posterior de nivel
            categoriaActual = nombreCategoria;
            
            // Ocultar los botones de categorías
            const botonesDiv = document.querySelector('.botones-categorias');
            const tituloCategoria = document.querySelector('.titulo-categorias');
            const botonRegresar = document.querySelector('.boton-regresar');
            
            botonesDiv.style.display = 'none';
            botonRegresar.style.display = 'none';
            
            // Actualizar título para mostrar categoría seleccionada
            tituloCategoria.innerHTML = `🎯 ${nombreCategoria} - Selecciona un Tema`;
            
            // Crear loading para temas
            const loadingTemas = document.createElement('div');
            loadingTemas.className = 'loading-temas';
            loadingTemas.innerHTML = `
                <div class="spinner"></div>
                <p>Cargando temas disponibles...</p>
            `;
            
            // Crear contenedor para botones de temas
            const contenedorTemas = document.createElement('div');
            contenedorTemas.className = 'botones-temas';
            contenedorTemas.style.display = 'none';
            
            // Crear botón para regresar a categorías
            const botonRegresarCategorias = document.createElement('button');
            botonRegresarCategorias.className = 'boton-regresar-categorias';
            botonRegresarCategorias.innerHTML = '← Regresar a Categorías';
            botonRegresarCategorias.onclick = () => regresarACategorias(idCategoria, nombreCategoria);
            
            // Insertar elementos en el contenedor
            const contenedorCategorias = document.querySelector('.contenedor-categorias');
            contenedorCategorias.appendChild(loadingTemas);
            contenedorCategorias.appendChild(contenedorTemas);
            contenedorCategorias.appendChild(botonRegresarCategorias);
            
            // Cargar temas desde el servidor
            cargarTemas(idCategoria, nombreCategoria);
        }

        // funcion para cargar temas de una categoria especifica
        function cargarTemas(idCategoria, nombreCategoria) {
            const formData = new FormData();
            formData.append('categoria_id', idCategoria);
            
            fetch('cargarTemas.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    const loadingDiv = document.querySelector('.loading-temas');
                    const temasDiv = document.querySelector('.botones-temas');
                    
                    if (data.status === 'success') {
                        // Ocultar loading
                        loadingDiv.style.display = 'none';
                        
                        // Crear botones de temas
                        let temasHTML = '';
                        data.data.forEach(tema => {
                            const iconosTemas = {
                                'PHP': '🐘',
                                'Laravel': '🔴',
                                'CRUD': '📝',
                                'JavaScript': '🟡',
                                'MySQL': '🗃️',
                                'CSS': '🎨',
                                'HTML': '📄'
                            };
                            
                            const coloresTemas = {
                                'PHP': 'tema-php',
                                'Laravel': 'tema-laravel',
                                'CRUD': 'tema-crud',
                                'JavaScript': 'tema-js',
                                'MySQL': 'tema-mysql',
                                'CSS': 'tema-css',
                                'HTML': 'tema-html'
                            };
                            
                            const iconoTema = iconosTemas[tema.nombre_tema] || '📚';
                            const colorTema = coloresTemas[tema.nombre_tema] || 'tema-default';
                            
                            temasHTML += `
                                <button class="boton-tema ${colorTema}" 
                                        onclick="seleccionarTema(${tema.id_tema}, '${tema.nombre_tema}', ${idCategoria}, '${nombreCategoria}')">
                                    <span class="icono-tema">${iconoTema}</span>
                                    <span class="nombre-tema">${tema.nombre_tema}</span>
                                    <span class="descripcion-tema">
                                        ${tema.descripcion || 'Preguntas de ' + tema.nombre_tema}
                                    </span>
                                </button>
                            `;
                        });
                        
                        temasDiv.innerHTML = temasHTML;
                        temasDiv.style.display = 'grid';
                        
                    } else {
                        loadingDiv.innerHTML = `
                            <div class="error-loading">
                                <p>❌ ${data.message}</p>
                                <p>No hay temas disponibles para esta categoría</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const loadingDiv = document.querySelector('.loading-temas');
                    loadingDiv.innerHTML = `
                        <div class="error-loading">
                            <p>❌ Error de conexión</p>
                            <p>No se pudieron cargar los temas</p>
                        </div>
                    `;
                });
        }

        // funcion para regresar a la seleccion de categorias
        function regresarACategorias(idCategoria, nombreCategoria) {
            console.log('Regresando a categorías...');
            
            // Eliminar elementos de temas
            const loadingTemas = document.querySelector('.loading-temas');
            const contenedorTemas = document.querySelector('.botones-temas');
            const botonRegresarCategorias = document.querySelector('.boton-regresar-categorias');
            
            if (loadingTemas) loadingTemas.remove();
            if (contenedorTemas) contenedorTemas.remove();
            if (botonRegresarCategorias) botonRegresarCategorias.remove();
            
            // Restaurar título original
            const tituloCategoria = document.querySelector('.titulo-categorias');
            tituloCategoria.innerHTML = '🎯 Selecciona una Categoría';
            
            // Mostrar botones de categorías y botón regresar original
            const botonesDiv = document.querySelector('.botones-categorias');
            const botonRegresar = document.querySelector('.boton-regresar');
            
            botonesDiv.style.display = 'grid';
            botonRegresar.style.display = 'block';
        }

        // funcion para seleccionar tema y comenzar trivia
        function seleccionarTema(idTema, nombreTema, idCategoria, nombreCategoria) {
            console.log(`Tema seleccionado: ${nombreTema} (ID: ${idTema}) en categoría ${nombreCategoria}`);
            
            // Ocultar la selección de temas
            const loadingTemas = document.querySelector('.loading-temas');
            const contenedorTemas = document.querySelector('.botones-temas');
            const botonRegresarCategorias = document.querySelector('.boton-regresar-categorias');
            const tituloCategoria = document.querySelector('.titulo-categorias');
            
            if (loadingTemas) loadingTemas.style.display = 'none';
            if (contenedorTemas) contenedorTemas.style.display = 'none';
            if (botonRegresarCategorias) botonRegresarCategorias.style.display = 'none';
            
            // Actualizar título para mostrar el tema seleccionado
            tituloCategoria.innerHTML = `🎮 ${nombreCategoria} - ${nombreTema}`;
            
            // Crear contenedor para la trivia
            const contenedorTrivia = document.createElement('div');
            contenedorTrivia.className = 'contenedor-trivia';
            contenedorTrivia.innerHTML = `
                <div class="loading-preguntas">
                    <div class="spinner"></div>
                    <p>Cargando preguntas...</p>
                </div>
                <div class="juego-trivia" style="display: none;">
                    <div class="info-juego">
                        <div class="contador-pregunta">
                            <span class="pregunta-actual">1</span> / <span class="total-preguntas">0</span>
                        </div>
                        <div class="temporizador">
                            ⏱️ <span class="tiempo-transcurrido">00:00</span>
                        </div>
                        <div class="puntos-acumulados">
                            Puntos: <span class="puntos">0</span>
                        </div>
                    </div>
                    <div class="pregunta-contenedor">
                        <h4 class="texto-pregunta"></h4>
                        <div class="respuestas-contenedor"></div>
                    </div>
                    <div class="controles-juego">
                        <button class="boton-siguiente" onclick="siguientePregunta()" style="display: none;">
                            Siguiente Pregunta →
                        </button>
                        <button class="boton-terminar" onclick="terminarTrivia()" style="display: none;">
                            Terminar Trivia
                        </button>
                    </div>
                </div>
                <button class="boton-regresar-tema" onclick="regresarATemas(${idCategoria}, '${nombreCategoria}')">
                    ← Regresar a Temas
                </button>
            `;
            
            // Insertar contenedor de trivia
            const contenedorCategorias = document.querySelector('.contenedor-categorias');
            contenedorCategorias.appendChild(contenedorTrivia);
            
            // Cargar preguntas del tema
            cargarPreguntas(idTema, idCategoria, nombreTema, nombreCategoria);
        }

        // Variables globales para el juego
        let preguntasTrivia = [];
        let preguntaActual = 0;
        let puntosAcumulados = 0;
        let respuestaSeleccionada = null;
        let tiempoInicio = null;
        let tiempoFinal = null;
        let temporizadorIntervalo = null;
        let categoriaActual = null; // Para rastrear la categoría de la trivia actual
        let nivelUsuarioActual = '<?php echo addslashes($usuario_nivel); ?>'; // Nivel actual del usuario

        // funcion para cargar preguntas del tema seleccionado
        function cargarPreguntas(idTema, idCategoria, nombreTema, nombreCategoria) {
            const formData = new FormData();
            formData.append('tema_id', idTema);
            formData.append('categoria_id', idCategoria);
            
            fetch('cargarPreguntas.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    const loadingDiv = document.querySelector('.loading-preguntas');
                    const juegoDiv = document.querySelector('.juego-trivia');
                    
                    if (data.status === 'success') {
                        // Ocultar loading
                        loadingDiv.style.display = 'none';
                        
                        // Configurar datos del juego
                        preguntasTrivia = data.data;
                        preguntaActual = 0;
                        puntosAcumulados = 0;
                        tiempoInicio = null;
                        tiempoFinal = null;
                        
                        // Actualizar contador total de preguntas
                        document.querySelector('.total-preguntas').textContent = preguntasTrivia.length;
                        
                        // Mostrar juego y cargar primera pregunta
                        juegoDiv.style.display = 'block';
                        mostrarPregunta();
                        
                    } else {
                        loadingDiv.innerHTML = `
                            <div class="error-loading">
                                <p>❌ ${data.message}</p>
                                <p>No se pudieron cargar las preguntas</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const loadingDiv = document.querySelector('.loading-preguntas');
                    loadingDiv.innerHTML = `
                        <div class="error-loading">
                            <p>❌ Error de conexión</p>
                            <p>No se pudieron cargar las preguntas</p>
                        </div>
                    `;
                });
        }

        // funcion para mostrar la pregunta actual
        function mostrarPregunta() {
            const pregunta = preguntasTrivia[preguntaActual];
            
            // Iniciar temporizador en la primera pregunta
            if (preguntaActual === 0 && tiempoInicio === null) {
                tiempoInicio = new Date();
                iniciarTemporizador();
                console.log('⏱️ Temporizador iniciado:', tiempoInicio);
            }
            
            // Actualizar contador de pregunta
            document.querySelector('.pregunta-actual').textContent = preguntaActual + 1;
            document.querySelector('.puntos').textContent = puntosAcumulados;
            
            // Mostrar texto de la pregunta
            document.querySelector('.texto-pregunta').textContent = pregunta.texto_pregunta;
            
            // Crear botones de respuesta
            const respuestasContainer = document.querySelector('.respuestas-contenedor');
            respuestasContainer.innerHTML = '';
            
            pregunta.respuestas.forEach((respuesta, index) => {
                const botonRespuesta = document.createElement('button');
                botonRespuesta.className = 'boton-respuesta';
                botonRespuesta.textContent = respuesta.texto_respuesta;
                botonRespuesta.onclick = () => seleccionarRespuesta(index, respuesta.es_correcta);
                
                respuestasContainer.appendChild(botonRespuesta);
            });
            
            // Resetear estado de respuesta
            respuestaSeleccionada = null;
            document.querySelector('.boton-siguiente').style.display = 'none';
            document.querySelector('.boton-terminar').style.display = 'none';
        }

        // funcion para seleccionar una respuesta
        function seleccionarRespuesta(indiceRespuesta, esCorrecta) {
            if (respuestaSeleccionada !== null) return; // Ya se seleccionó una respuesta
            
            respuestaSeleccionada = indiceRespuesta;
            const botonesRespuesta = document.querySelectorAll('.boton-respuesta');
            
            // Si es la última pregunta, detener el temporizador
            if (preguntaActual === preguntasTrivia.length - 1) {
                tiempoFinal = new Date();
                detenerTemporizador();
                console.log('⏱️ Temporizador detenido:', tiempoFinal);
            }
            
            // Marcar todas las respuestas y mostrar cuál es correcta
            preguntasTrivia[preguntaActual].respuestas.forEach((respuesta, index) => {
                const boton = botonesRespuesta[index];
                
                if (respuesta.es_correcta) {
                    boton.classList.add('respuesta-correcta');
                } else if (index === indiceRespuesta) {
                    boton.classList.add('respuesta-incorrecta');
                } else {
                    boton.classList.add('respuesta-no-seleccionada');
                }
            });
            
            // Actualizar puntos si es correcta
            if (esCorrecta) {
                puntosAcumulados += preguntasTrivia[preguntaActual].puntos;
                document.querySelector('.puntos').textContent = puntosAcumulados;
            }
            
            // Mostrar botón para continuar
            if (preguntaActual < preguntasTrivia.length - 1) {
                document.querySelector('.boton-siguiente').style.display = 'inline-block';
            } else {
                document.querySelector('.boton-terminar').style.display = 'inline-block';
            }
        }

        // funcion para avanzar a la siguiente pregunta
        function siguientePregunta() {
            preguntaActual++;
            mostrarPregunta();
        }

        // funcion para iniciar el temporizador
        function iniciarTemporizador() {
            temporizadorIntervalo = setInterval(actualizarTemporizador, 1000);
            // Agregar clase para animación
            const temporizadorElement = document.querySelector('.temporizador');
            if (temporizadorElement) {
                temporizadorElement.classList.add('activo');
            }
        }

        // funcion para actualizar el display del temporizador
        function actualizarTemporizador() {
            if (tiempoInicio) {
                const tiempoActual = new Date();
                const tiempoTranscurrido = Math.floor((tiempoActual - tiempoInicio) / 1000);
                const minutos = Math.floor(tiempoTranscurrido / 60);
                const segundos = tiempoTranscurrido % 60;
                
                const tiempoFormateado = `${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
                document.querySelector('.tiempo-transcurrido').textContent = tiempoFormateado;
            }
        }

        // funcion para detener el temporizador
        function detenerTemporizador() {
            if (temporizadorIntervalo) {
                clearInterval(temporizadorIntervalo);
                temporizadorIntervalo = null;
            }
            // Quitar clase de animación
            const temporizadorElement = document.querySelector('.temporizador');
            if (temporizadorElement) {
                temporizadorElement.classList.remove('activo');
            }
        }

        // funcion para calcular el tiempo total transcurrido
        function calcularTiempoTotal() {
            if (tiempoInicio && tiempoFinal) {
                return Math.floor((tiempoFinal - tiempoInicio) / 1000);
            }
            return 0;
        }

        // funcion para terminar la trivia
        async function terminarTrivia() {
            // Asegurar que el temporizador esté detenido
            detenerTemporizador();
            
            const tiempoTotalSegundos = calcularTiempoTotal();
            const minutosTotales = Math.floor(tiempoTotalSegundos / 60);
            const segundosTotales = tiempoTotalSegundos % 60;
            const tiempoFormateado = `${minutosTotales}:${segundosTotales.toString().padStart(2, '0')}`;
            
            const porcentaje = ((puntosAcumulados / (preguntasTrivia.length * 10)) * 100).toFixed(1);
            
            // Verificar si puede subir de nivel (solo si tiene 100% de aciertos)
            let resultadoNivel = { actualizado: false };
            if (porcentaje == 100 && categoriaActual) {
                console.log(`🔍 Verificando posible subida de nivel: ${nivelUsuarioActual} completando ${categoriaActual} con ${porcentaje}%`);
                resultadoNivel = await verificarActualizacionNivel(categoriaActual, porcentaje);
            }
            
            let mensaje = `🎉 ¡Trivia Completada! 🎉\n\n`;
            mensaje += `📊 Puntuación: ${puntosAcumulados} / ${preguntasTrivia.length * 10} puntos\n`;
            mensaje += `📈 Porcentaje: ${porcentaje}%\n`;
            mensaje += `⏱️ Tiempo total: ${tiempoFormateado}\n\n`;
            
            // Agregar mensaje especial si subió de nivel
            if (resultadoNivel.actualizado) {
                mensaje += `🎊 ¡FELICIDADES! 🎊\n`;
                mensaje += `📈 Has subido de nivel: ${resultadoNivel.nivelAnterior} → ${resultadoNivel.nivelNuevo}\n\n`;
            }
            
            if (porcentaje >= 80) {
                mensaje += `🏆 ¡Excelente! Eres un experto en este tema.`;
            } else if (porcentaje >= 60) {
                mensaje += `👍 ¡Bien hecho! Tienes un buen conocimiento.`;
            } else if (porcentaje >= 40) {
                mensaje += `📚 No está mal, pero podrías estudiar un poco más.`;
            } else {
                mensaje += `💪 Sigue practicando, ¡puedes mejorar!`;
            }
            
            // Agregar mensaje especial para puntaje perfecto sin subida de nivel
            if (porcentaje == 100 && !resultadoNivel.actualizado) {
                mensaje += `\n\n⭐ ¡Puntuación perfecta!`;
                if (nivelUsuarioActual === categoriaActual) {
                    mensaje += ` Has dominado tu nivel actual.`;
                } else {
                    mensaje += ` ¡Ya tienes este nivel dominado!`;
                }
            }
            
            alert(mensaje);
            
            // Si el nivel fue actualizado, actualizar la interfaz
            if (resultadoNivel.actualizado) {
                actualizarInterfazNivel(resultadoNivel.nivelNuevo);
            }
            
            // Agregar a las estadísticas con el tiempo real
            agregarPuntosTiempo(puntosAcumulados, tiempoFormateado);
            
            // Regresar al panel principal
            regresarPanelPrincipal();
        }
        
        // Función para verificar si el usuario puede subir de nivel
        async function verificarActualizacionNivel(categoria, porcentaje) {
            try {
                const response = await fetch('actualizarNivel.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        categoria: categoria,
                        porcentaje: porcentaje
                    })
                });
                
                const resultado = await response.json();
                
                if (resultado.success && resultado.actualizado) {
                    // Actualizar variable global del nivel
                    nivelUsuarioActual = resultado.nivelNuevo;
                    console.log(`🎊 Nivel actualizado: ${resultado.nivelAnterior} → ${resultado.nivelNuevo}`);
                }
                
                return resultado;
            } catch (error) {
                console.error('Error al verificar actualización de nivel:', error);
                return { actualizado: false };
            }
        }
        
        // Función para actualizar la interfaz cuando cambia el nivel
        function actualizarInterfazNivel(nuevoNivel) {
            console.log('🔄 Actualizando interfaz para nuevo nivel:', nuevoNivel);
            
            // Actualizar el nivel mostrado en el perfil
            const nivelUsuarioElement = document.querySelector('.nivel-usuario');
            if (nivelUsuarioElement) {
                nivelUsuarioElement.textContent = `Nivel: ${nuevoNivel}`;
            }
            
            // Actualizar el número de nivel en las estadísticas
            const numeroNivelElement = document.querySelector('.numero-estadistica');
            if (numeroNivelElement && numeroNivelElement.parentElement.querySelector('.etiqueta-estadistica').textContent === 'Nivel') {
                let numeroNivel = '';
                switch(nuevoNivel) {
                    case 'Principiante':
                        numeroNivel = '1';
                        break;
                    case 'Novato':
                        numeroNivel = '2';
                        break;
                    case 'Experto':
                        numeroNivel = '3';
                        break;
                    default:
                        numeroNivel = '-';
                }
                numeroNivelElement.textContent = numeroNivel;
            }
            
            // Mostrar una animación de celebración
            mostrarAnimacionSubidaNivel(nuevoNivel);
        }

        // Función para mostrar animación de subida de nivel
        function mostrarAnimacionSubidaNivel(nuevoNivel) {
            // Crear elemento de animación
            const animacion = document.createElement('div');
            animacion.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: 9999;
                background: linear-gradient(135deg, #FFD700, #FFA500);
                color: #333;
                padding: 30px;
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                text-align: center;
                font-size: 18px;
                font-weight: bold;
                animation: levelUpAnimation 3s ease-in-out;
                pointer-events: none;
            `;
            
            animacion.innerHTML = `
                <div style="font-size: 48px; margin-bottom: 10px;">🎉</div>
                <div>¡NIVEL ACTUALIZADO!</div>
                <div style="font-size: 24px; margin-top: 10px;">${nuevoNivel}</div>
            `;
            
            // Agregar estilos de animación
            const style = document.createElement('style');
            style.textContent = `
                @keyframes levelUpAnimation {
                    0% { opacity: 0; transform: translate(-50%, -50%) scale(0.5); }
                    20% { opacity: 1; transform: translate(-50%, -50%) scale(1.1); }
                    80% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
                    100% { opacity: 0; transform: translate(-50%, -50%) scale(0.8); }
                }
            `;
            document.head.appendChild(style);
            
            document.body.appendChild(animacion);
            
            // Remover después de la animación
            setTimeout(() => {
                animacion.remove();
                style.remove();
            }, 3000);
        }

        // funcion para regresar a la seleccion de temas
        function regresarATemas(idCategoria, nombreCategoria) {
            // Detener temporizador si está activo
            detenerTemporizador();
            
            // Eliminar contenedor de trivia
            const contenedorTrivia = document.querySelector('.contenedor-trivia');
            if (contenedorTrivia) {
                contenedorTrivia.remove();
            }
            
            // Restaurar título
            const tituloCategoria = document.querySelector('.titulo-categorias');
            tituloCategoria.innerHTML = `🎯 ${nombreCategoria} - Selecciona un Tema`;
            
            // Mostrar elementos de selección de temas
            const loadingTemas = document.querySelector('.loading-temas');
            const contenedorTemas = document.querySelector('.botones-temas');
            const botonRegresarCategorias = document.querySelector('.boton-regresar-categorias');
            
            if (loadingTemas) loadingTemas.style.display = 'block';
            if (contenedorTemas) contenedorTemas.style.display = 'grid';
            if (botonRegresarCategorias) botonRegresarCategorias.style.display = 'block';
            
            // Recargar temas
            cargarTemas(idCategoria, nombreCategoria);
        }

        // funcion para agregar monedas a la lista
        function agregarMoneda(cantidad) {
            const listaMonedas = document.getElementById('lista-monedas');
            const itemVacio = listaMonedas.querySelector('.lista-vacia');
            
            if (itemVacio) {
                itemVacio.remove();
            }
            
            const nuevoItem = document.createElement('li');
            nuevoItem.innerHTML = `💰 +${cantidad} monedas`;
            listaMonedas.appendChild(nuevoItem);
        }

        // funcion para agregar puntos y tiempo a la lista
        function agregarPuntosTiempo(puntos, tiempo) {
            const listaPuntosTiempo = document.getElementById('lista-puntos-tiempo');
            const itemVacio = listaPuntosTiempo.querySelector('.lista-vacia');
            
            if (itemVacio) {
                itemVacio.remove();
            }
            
            const nuevoItem = document.createElement('li');
            nuevoItem.innerHTML = `⭐ ${puntos} pts - ⏱️ ${tiempo}s`;
            listaPuntosTiempo.appendChild(nuevoItem);
        }

        // funcion para descargar estadisticas
        function descargarEstadisticas() {
            console.log('Descargando estadísticas...');
            
            // Crear contenido del archivo
            const estadisticas = {
                usuario: document.getElementById('username-display').textContent,
                fecha: new Date().toLocaleDateString(),
                monedas: Array.from(document.querySelectorAll('#lista-monedas li:not(.lista-vacia)')).map(li => li.textContent),
                puntosTiempo: Array.from(document.querySelectorAll('#lista-puntos-tiempo li:not(.lista-vacia)')).map(li => li.textContent)
            };
            
            const contenido = `
ESTADÍSTICAS DE TRIVIA - REICHMIND
=====================================
Usuario: ${estadisticas.usuario}
Fecha: ${estadisticas.fecha}

MONEDAS GANADAS:
${estadisticas.monedas.length > 0 ? estadisticas.monedas.join('\n') : 'No hay registros'}

PUNTOS Y TIEMPO:
${estadisticas.puntosTiempo.length > 0 ? estadisticas.puntosTiempo.join('\n') : 'No hay registros'}

Generado automáticamente por ReichMind
            `.trim();
            
            // Crear y descargar archivo
            const blob = new Blob([contenido], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `estadisticas_trivia_${new Date().toISOString().split('T')[0]}.txt`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            alert('📊 Estadísticas descargadas correctamente');
        }

        // Variables globales para el cambio de avatar
        let avatarSeleccionado = null;
        let archivoSeleccionado = null;
        let tipoSeleccion = null; // 'galeria' o 'archivo'

        // Función para abrir el modal de avatar
        function abrirModalAvatar() {
            console.log('🖼️ Abriendo modal de avatar...');
            const modal = document.getElementById('modalAvatar');
            modal.style.display = 'block';
            
            // Cargar imágenes de la galería
            cargarGaleriaAvatares();
            
            // Resetear selecciones
            avatarSeleccionado = null;
            archivoSeleccionado = null;
            tipoSeleccion = null;
            document.getElementById('botonGuardar').disabled = true;
            
            // Limpiar vista previa
            const vistaPrevia = document.getElementById('vistaPrevia');
            vistaPrevia.style.display = 'none';
        }

        // Función para cerrar el modal de avatar
        function cerrarModalAvatar() {
            const modal = document.getElementById('modalAvatar');
            modal.style.display = 'none';
            
            // Limpiar selecciones
            avatarSeleccionado = null;
            archivoSeleccionado = null;
            tipoSeleccion = null;
            
            // Limpiar vista previa
            const vistaPrevia = document.getElementById('vistaPrevia');
            vistaPrevia.style.display = 'none';
            const fileInput = document.getElementById('fileInput');
            fileInput.value = '';
        }

        // Función para cargar la galería de avatares desde la base de datos
        function cargarGaleriaAvatares() {
            fetch('cargarImagenes.php')
                .then(response => response.json())
                .then(data => {
                    const galeria = document.getElementById('galeriaAvatares');
                    
                    if (data.status === 'success') {
                        let galeriaHTML = '';
                        data.data.forEach(imagen => {
                            const activo = imagen.nombre_archivo === '<?php echo $usuario_avatar; ?>' ? 'seleccionado' : '';
                            galeriaHTML += `
                                <div class="avatar-opcion ${activo}" 
                                     style="background-image: url('../img/${imagen.nombre_archivo}')"
                                     onclick="seleccionarAvatarGaleria('${imagen.nombre_archivo}')"
                                     title="${imagen.nombre_display}">
                                </div>
                            `;
                        });
                        galeria.innerHTML = galeriaHTML;
                    } else {
                        galeria.innerHTML = '<p>No se pudieron cargar las imágenes de la galería</p>';
                    }
                })
                .catch(error => {
                    console.error('Error cargando galería:', error);
                    document.getElementById('galeriaAvatares').innerHTML = '<p>Error al cargar la galería</p>';
                });
        }

        // Función para seleccionar un avatar de la galería
        function seleccionarAvatarGaleria(nombreArchivo) {
            console.log('🖼️ Avatar seleccionado de galería:', nombreArchivo);
            
            // Remover selección anterior
            document.querySelectorAll('.avatar-opcion').forEach(el => {
                el.classList.remove('seleccionado');
            });
            
            // Seleccionar nuevo avatar
            event.target.classList.add('seleccionado');
            
            // Actualizar variables globales
            avatarSeleccionado = nombreArchivo;
            tipoSeleccion = 'galeria';
            archivoSeleccionado = null;
            
            // Ocultar vista previa de archivo
            document.getElementById('vistaPrevia').style.display = 'none';
            
            // Habilitar botón guardar
            document.getElementById('botonGuardar').disabled = false;
        }

        // Función para manejar archivo seleccionado
        function manejarArchivoSeleccionado(event) {
            const archivo = event.target.files[0];
            procesarArchivo(archivo);
        }

        // Funciones para drag and drop
        function manejarDragOver(event) {
            event.preventDefault();
        }

        function manejarDragEnter(event) {
            event.preventDefault();
            event.target.closest('.upload-area').classList.add('dragover');
        }

        function manejarDragLeave(event) {
            event.preventDefault();
            event.target.closest('.upload-area').classList.remove('dragover');
        }

        function manejarDrop(event) {
            event.preventDefault();
            event.target.closest('.upload-area').classList.remove('dragover');
            
            const archivos = event.dataTransfer.files;
            if (archivos.length > 0) {
                procesarArchivo(archivos[0]);
            }
        }

        // Función común para procesar archivos
        function procesarArchivo(archivo) {
            if (!archivo) return;
            
            // Validar tipo de archivo
            const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!tiposPermitidos.includes(archivo.type)) {
                alert('❌ Tipo de archivo no soportado. Use JPG, PNG o GIF.');
                return;
            }
            
            // Validar tamaño (5MB máximo)
            if (archivo.size > 5 * 1024 * 1024) {
                alert('❌ El archivo es muy grande. Máximo 5MB permitido.');
                return;
            }
            
            console.log('📁 Archivo seleccionado:', archivo.name);
            
            // Mostrar vista previa
            const reader = new FileReader();
            reader.onload = function(e) {
                const imagenPrevia = document.getElementById('imagenPrevia');
                imagenPrevia.src = e.target.result;
                document.getElementById('vistaPrevia').style.display = 'block';
            };
            reader.readAsDataURL(archivo);
            
            // Actualizar variables globales
            archivoSeleccionado = archivo;
            tipoSeleccion = 'archivo';
            avatarSeleccionado = null;
            
            // Remover selección de galería
            document.querySelectorAll('.avatar-opcion').forEach(el => {
                el.classList.remove('seleccionado');
            });
            
            // Habilitar botón guardar
            document.getElementById('botonGuardar').disabled = false;
        }

        // Función para guardar el avatar seleccionado
        function guardarAvatar() {
            if (!avatarSeleccionado && !archivoSeleccionado) {
                alert('❌ Selecciona un avatar primero');
                return;
            }
            
            console.log('💾 Guardando avatar...', { tipo: tipoSeleccion, avatar: avatarSeleccionado });
            
            const formData = new FormData();
            
            if (tipoSeleccion === 'galeria') {
                // Guardar avatar de galería
                formData.append('tipo', 'galeria');
                formData.append('avatar', avatarSeleccionado);
                formData.append('usuario_id', '<?php echo $usuario_id; ?>');
                
                enviarCambioAvatar(formData);
                
            } else if (tipoSeleccion === 'archivo') {
                // Subir archivo personalizado
                formData.append('tipo', 'archivo');
                formData.append('usuario_id', '<?php echo $usuario_id; ?>');
                formData.append('archivo_avatar', archivoSeleccionado);
                
                enviarCambioAvatar(formData);
            }
        }

        // Función para enviar el cambio de avatar al servidor
        function enviarCambioAvatar(formData) {
            // Deshabilitar botón durante el proceso
            const botonGuardar = document.getElementById('botonGuardar');
            botonGuardar.disabled = true;
            botonGuardar.textContent = 'Guardando...';
            
            fetch('cambiarAvatar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('✅ Avatar actualizado correctamente');
                    
                    // Recargar la página para mostrar el nuevo avatar
                    window.location.reload();
                    
                } else {
                    alert('❌ Error al actualizar avatar: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error de conexión al actualizar avatar');
            })
            .finally(() => {
                // Restaurar botón
                botonGuardar.disabled = false;
                botonGuardar.textContent = 'Guardar Cambios';
            });
        }

        // Cerrar modal al hacer clic fuera de él
        window.onclick = function(event) {
            const modalAvatar = document.getElementById('modalAvatar');
            const modalBloqueo = document.getElementById('modalBloqueo');
            
            if (event.target === modalAvatar) {
                cerrarModalAvatar();
            }
            
            if (event.target === modalBloqueo) {
                cerrarModalBloqueo();
            }
        }

        // Función para cerrar sesión
        function cerrarSesion() {
            // Mostrar confirmación
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                // Limpiar variables del juego si están activas
                if (temporizadorIntervalo) {
                    detenerTemporizador();
                }
                
                // Limpiar localStorage si existe
                if (typeof(Storage) !== "undefined") {
                    localStorage.clear();
                }
                
                // Redirigir a página de logout que destruye la sesión
                window.location.href = 'logout.php';
            }
        }
    </script>
</body>
</html>
