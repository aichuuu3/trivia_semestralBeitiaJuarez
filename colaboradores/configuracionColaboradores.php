<?php
session_start();
require_once '../bd/claseBD.php';

// Asegúrate de que el colaborador esté logueado y su id esté en la sesión
if (!isset($_SESSION['colaborador_id'])) {
    die('No autorizado');
}
$colaborador_id = $_SESSION['colaborador_id'];
$db = new DB();

// Actualizar campos si se envía el formulario (nombre, email, password)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_completo'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $avatar = $_POST['avatar'] ?? null;
    $mensaje_error = '';
    $avatar_actualizado = false;
    
    // Manejar subida de nueva imagen
    if (isset($_FILES['nueva_imagen']) && $_FILES['nueva_imagen']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['nueva_imagen'];
        $nombre_original = $archivo['name'];
        $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
        $tamaño_bytes = $archivo['size'];
        $tamaño_kb = round($tamaño_bytes / 1024);
        
        // Validar tipo de archivo
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $extensiones_permitidas)) {
            $mensaje_error = 'Solo se permiten archivos de imagen (JPG, PNG, GIF, WEBP)';
        } else if ($tamaño_bytes > 5 * 1024 * 1024) { // 5MB máximo
            $mensaje_error = 'El archivo es demasiado grande. Máximo 5MB.';
        } else {
            // Generar nombre único para evitar conflictos
            $nombre_archivo = 'avatar_colaborador_' . $colaborador_id . '_' . time() . '.' . $extension;
            $ruta_destino = '../img/' . $nombre_archivo;
            $ruta_completa = '../img/' . $nombre_archivo;
            
            // Mover archivo subido
            if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
                // Insertar en base de datos
                $nombre_display = 'Avatar Colaborador Personalizado';
                if ($db->insertarImagen($nombre_archivo, $nombre_display, $ruta_completa, $extension, $tamaño_kb)) {
                    // Actualizar avatar del colaborador
                    if ($db->actualizarAvatarColaborador($colaborador_id, $nombre_archivo)) {
                        $avatar_actualizado = true;
                    } else {
                        $mensaje_error = 'Error al actualizar el avatar en la base de datos';
                        unlink($ruta_destino); // Eliminar archivo si falla BD
                    }
                } else {
                    $mensaje_error = 'Error al guardar la imagen en la base de datos';
                    unlink($ruta_destino); // Eliminar archivo si falla BD
                }
            } else {
                $mensaje_error = 'Error al subir el archivo';
            }
        }
    }
    // Manejar actualización de avatar predefinido (solo si no se subió archivo nuevo)
    else if ($avatar && !$avatar_actualizado) {
        if ($db->actualizarAvatarColaborador($colaborador_id, $avatar)) {
            $avatar_actualizado = true;
        } else {
            $mensaje_error = 'Error al actualizar el avatar';
        }
    }
    
    // Actualizar otros campos (nombre, email, password)
    if ($nombre && $email && !$mensaje_error) {
        $result = $db->actualizarColaborador($colaborador_id, $nombre, $email, $password);
        if (!$result) {
            $mensaje_error = 'Error al actualizar los datos del colaborador';
        }
    }
    
    // Redirigir para evitar reenvío con mensaje de éxito o error
    if ($mensaje_error) {
        header('Location: configuracionColaboradores.php?tab=edit&error=' . urlencode($mensaje_error));
    } else {
        header('Location: configuracionColaboradores.php?tab=edit&updated=1');
    }
    exit();
}

// Obtener datos del colaborador
$colaborador = $db->obtenerColaborador($colaborador_id);
if (!$colaborador) {
    die('Colaborador no encontrado');
}

// Obtener avatares disponibles
$pdo = $db->getPdo();
$stmt = $pdo->query("SELECT nombre_archivo, nombre_display FROM imagenes WHERE tipo_imagen = 'avatar' AND activa = 1");
$avatares_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Colaborador</title>
    <link rel="stylesheet" href="../css/configuracion.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
    // Script para cambiar pestañas sin recargar
    document.addEventListener('DOMContentLoaded', function() {
        // Manejar clics en pestañas
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                const tabs = document.querySelectorAll('.tab');
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                const tabName = this.getAttribute('data-tab');
                window.location.search = '?tab=' + tabName;
            });
        });
        
        // Manejar envío del formulario para avatar
        const form = document.getElementById('form-editar-colaborador');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Permitir que el formulario se envíe normalmente
                // No prevenir el comportamiento por defecto
            });
        }
        
        // Mostrar mensaje de éxito si se actualizó
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('updated') === '1') {
            // Crear notificación de éxito
            const notification = document.createElement('div');
            notification.innerHTML = '✅ Perfil actualizado correctamente';
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #28a745;
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                z-index: 1000;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                font-weight: 500;
            `;
            document.body.appendChild(notification);
            
            // Remover notificación después de 3 segundos
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
            
            // Limpiar URL
            const newUrl = window.location.pathname + '?tab=edit';
            window.history.replaceState({}, '', newUrl);
        }
        
        // Mostrar mensaje de error si existe
        if (urlParams.get('error')) {
            const notification = document.createElement('div');
            notification.innerHTML = '❌ ' + decodeURIComponent(urlParams.get('error'));
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #dc3545;
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                z-index: 1000;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                font-weight: 500;
                max-width: 400px;
            `;
            document.body.appendChild(notification);
            
            // Remover notificación después de 5 segundos
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
            
            // Limpiar URL
            const newUrl = window.location.pathname + '?tab=edit';
            window.history.replaceState({}, '', newUrl);
        }
        
        // Manejar subida de archivo
        const fileInput = document.getElementById('nueva_imagen');
        const filePreview = document.getElementById('file-preview');
        const btnSubir = document.getElementById('btn-subir');
        
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validar tipo de archivo
                    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                    if (!validTypes.includes(file.type)) {
                        alert('Solo se permiten archivos de imagen (JPG, PNG, GIF, WEBP)');
                        this.value = '';
                        return;
                    }
                    
                    // Validar tamaño (5MB máximo)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('El archivo es demasiado grande. Máximo 5MB.');
                        this.value = '';
                        return;
                    }
                    
                    // Mostrar preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        filePreview.innerHTML = `
                            <div class="file-preview-item">
                                <img src="${e.target.result}" alt="Preview" style="width: 50px; height: 50px; border-radius: 6px; object-fit: cover; margin-top: 8px;">
                                <span style="margin-left: 10px; font-size: 0.9em;">${file.name}</span>
                            </div>
                        `;
                        btnSubir.style.display = 'inline-block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    filePreview.innerHTML = '';
                    btnSubir.style.display = 'none';
                }
            });
        }
    });
    </script>
</head>
<body>
    <header class="header">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-star"></i>
            </div>
            <span>ReichMind</span>
        </div>
        <button class="logout-btn" onclick="window.location.href='../colaboradores/loginColaboradores.php'">
            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
        </button>
    </header>
    <div class="main-container">
        <?php include '../sidebar/sbcolaborador.php'; ?>
        <main class="main-content">
            <div class="profile-container">
                <div class="profile-tabs">
                    <div class="tab<?php if($tab=='dashboard') echo ' active'; ?>" data-tab="dashboard">Dashboard</div>
                    <div class="tab<?php if($tab=='edit') echo ' active'; ?>" data-tab="edit">Edit Profile</div>
                </div>
                <div class="profile-card">
                    <div class="profile-img">
                        <img src="../img/<?php echo htmlspecialchars($colaborador['avatar'] ?? 'avatar.png'); ?>?v=<?php echo time(); ?>" alt="Foto de perfil" />
                        <?php if($tab=='edit'): ?>
                            <div class="avatar-edit-overlay">
                                <i class="fas fa-camera"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h2 class="profile-name"><?php echo htmlspecialchars($colaborador['nombre']); ?></h2>
                    <div class="profile-username">Colaborador ID: <?php echo $colaborador['id']; ?>
                        <?php if($tab=='edit'): ?> <i class="fas fa-pen edit-icon"></i><?php endif; ?>
                    </div>
                    <div class="profile-info-list">
                        <?php if($tab=='edit'): ?>
                        <form id="form-editar-colaborador" method="post" action="" enctype="multipart/form-data">
                        <?php endif; ?>
                            <div class="profile-info-row">
                                <span class="profile-info-label">Nombre</span>
                                <span class="profile-info-value" id="nombre-value">
                                    <?php if($tab=='edit'): ?>
                                        <input type="text" name="nombre_completo" value="<?php echo htmlspecialchars($colaborador['nombre']); ?>" style="width: 80%;">
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($colaborador['nombre']); ?>
                                    <?php endif; ?>
                                </span>
                                <?php if($tab=='edit'): ?><button type="submit" name="guardar_nombre" class="edit-save-btn"><i class="fas fa-save"></i></button><?php endif; ?>
                            </div>
                            <div class="profile-info-row">
                                <span class="profile-info-label">Email</span>
                                <span class="profile-info-value" id="email-value">
                                    <?php if($tab=='edit'): ?>
                                        <input type="email" name="email" value="<?php echo htmlspecialchars($colaborador['email']); ?>" style="width: 80%;">
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($colaborador['email']); ?>
                                    <?php endif; ?>
                                </span>
                                <?php if($tab=='edit'): ?><button type="submit" name="guardar_email" class="edit-save-btn"><i class="fas fa-save"></i></button><?php endif; ?>
                            </div>
                            <div class="profile-info-row">
                                <span class="profile-info-label">Contraseña</span>
                                <span class="profile-info-value" id="password-value">
                                    <?php if($tab=='edit'): ?>
                                        <input type="password" name="password" value="" placeholder="Nueva contraseña" style="width: 80%;">
                                    <?php else: ?>
                                        ********
                                    <?php endif; ?>
                                </span>
                                <?php if($tab=='edit'): ?><button type="submit" name="guardar_password" class="edit-save-btn"><i class="fas fa-save"></i></button><?php endif; ?>
                            </div>
                            <div class="profile-info-row">
                                <span class="profile-info-label">Fecha de registro</span>
                                <span class="profile-info-value">
                                    <?php 
                                    echo isset($colaborador['fecha_registro']) ? date('d/m/Y', strtotime($colaborador['fecha_registro'])) : '2024-01-01'; 
                                    ?>
                                </span>
                            </div>
                            <?php if($tab=='edit'): ?>
                            <div class="profile-info-row">
                                <span class="profile-info-label">Avatar</span>
                                <span class="profile-info-value">
                                    <div class="avatar-selection">
                                        <?php foreach($avatares_disponibles as $avatar): ?>
                                            <label class="avatar-option">
                                                <input type="radio" name="avatar" value="<?php echo htmlspecialchars($avatar['nombre_archivo']); ?>" 
                                                       <?php echo ($colaborador['avatar'] === $avatar['nombre_archivo']) ? 'checked' : ''; ?>>
                                                <img src="../img/<?php echo htmlspecialchars($avatar['nombre_archivo']); ?>?v=<?php echo time(); ?>" 
                                                     alt="<?php echo htmlspecialchars($avatar['nombre_display']); ?>"
                                                     title="<?php echo htmlspecialchars($avatar['nombre_display']); ?>">
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </span>
                                <button type="submit" name="guardar_avatar" class="edit-save-btn"><i class="fas fa-save"></i></button>
                            </div>
                            <div class="profile-info-row">
                                <span class="profile-info-label">Subir nueva imagen</span>
                                <span class="profile-info-value">
                                    <div class="upload-section">
                                        <input type="file" id="nueva_imagen" name="nueva_imagen" accept="image/*" style="display: none;">
                                        <label for="nueva_imagen" class="upload-btn">
                                            <i class="fas fa-upload"></i> Seleccionar imagen
                                        </label>
                                        <div class="upload-info">
                                            <small>Formatos: JPG, PNG, GIF, WEBP. Máximo 5MB.</small>
                                            <div id="file-preview"></div>
                                        </div>
                                    </div>
                                </span>
                                <button type="submit" name="subir_imagen" class="edit-save-btn" id="btn-subir" style="display: none;">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </button>
                            </div>
                            <?php endif; ?>
                        <?php if($tab=='edit'): ?>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
