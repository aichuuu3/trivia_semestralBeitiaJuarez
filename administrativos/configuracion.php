<?php
session_start();
require_once '../bd/claseBD.php';

// Asegúrate de que el admin esté logueado y su id esté en la sesión
if (!isset($_SESSION['admin_id'])) {
    die('No autorizado');
}
$admin_id = $_SESSION['admin_id'];
$db = new DB();

// Actualizar campos si se envía el formulario (nombre, email, password)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_completo'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    if ($nombre && $email) {
        $db->actualizarAdministrador($admin_id, $nombre, $email, $password);
    }
    // Redirigir para evitar reenvío
    header('Location: configuracion.php?tab=edit');
    exit();
}

// Sumar 1 segundo a horas_totales si se llama por AJAX
if (isset($_GET['sumar_hora'])) {
    $db->sumarHorasAdministrador($admin_id, 1);
    echo 'ok';
    exit();
}

// Obtener datos del admin
$admin = $db->obtenerAdministrador($admin_id);
if (!$admin) {
    die('Admin no encontrado');
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Administrativo</title>
    <link rel="stylesheet" href="../css/configuracion.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
    // Script para cambiar pestañas sin recargar
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const tabs = document.querySelectorAll('.tab');
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                const tabName = this.getAttribute('data-tab');
                window.location.search = '?tab=' + tabName;
            });
        });
    });
    // Sumar 1 segundo cada segundo y actualizar el contador en vivo
    setInterval(function() {
        fetch('configuracion.php?sumar_hora=1')
            .then(r => r.text())
            .then(txt => {
                if(txt==='ok') {
                    let el = document.getElementById('horas_totales');
                    if(el) {
                        let s = parseInt(el.dataset.segundos)+1;
                        el.dataset.segundos = s;
                        let h = Math.floor(s/3600);
                        let m = Math.floor((s%3600)/60);
                        let sec = s%60;
                        el.textContent = h+":"+m.toString().padStart(2,'0')+":"+sec.toString().padStart(2,'0');
                    }
                }
            });
    }, 1000);
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
        <button class="logout-btn" onclick="window.location.href='../administrativos/loginAdmin.php'">
            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
        </button>
    </header>
    <div class="main-container">
        <?php include '../sidebar/sb.php'; ?>
        <main class="main-content">
            <div class="profile-container">
                <div class="profile-tabs">
                    <div class="tab<?php if($tab=='dashboard') echo ' active'; ?>" data-tab="dashboard">Dashboard</div>
                    <div class="tab<?php if($tab=='edit') echo ' active'; ?>" data-tab="edit">Edit Profile</div>
                </div>
                <div class="profile-card">
                    <div class="profile-img">
                        <img src="../img/admin_default.png" alt="Foto de perfil" />
                    </div>
                    <h2 class="profile-name"><?php echo htmlspecialchars($admin['nombre']); ?></h2>
                    <div class="profile-username">Admin ID: <?php echo $admin['id']; ?>
                        <?php if($tab=='edit'): ?> <i class="fas fa-pen edit-icon"></i><?php endif; ?>
                    </div>
                    <div class="profile-info-list">
                        <form id="form-editar-admin" method="post" action="">
                            <div class="profile-info-row">
                                <span class="profile-info-label">Nombre</span>
                                <span class="profile-info-value" id="nombre-value">
                                    <?php if($tab=='edit'): ?>
                                        <input type="text" name="nombre_completo" value="<?php echo htmlspecialchars($admin['nombre']); ?>" style="width: 80%;">
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($admin['nombre']); ?>
                                    <?php endif; ?>
                                </span>
                                <?php if($tab=='edit'): ?><button type="submit" name="guardar_nombre" class="edit-save-btn"><i class="fas fa-save"></i></button><?php endif; ?>
                            </div>
                            <div class="profile-info-row">
                                <span class="profile-info-label">Email</span>
                                <span class="profile-info-value" id="email-value">
                                    <?php if($tab=='edit'): ?>
                                        <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" style="width: 80%;">
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($admin['email']); ?>
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
                                    echo isset($admin['fecha_registro']) ? date('d/m/Y', strtotime($admin['fecha_registro'])) : '2024-01-01'; 
                                    ?>
                                </span>
                            </div>
                            <div class="profile-info-row">
                                <span class="profile-info-label">Horas totales</span>
                                <span class="profile-info-value">
                                    <span id="horas_totales" data-segundos="<?php echo (int)$admin['horas_totales']; ?>">
                                    <?php
                                    $s = (int)$admin['horas_totales'];
                                    $h = floor($s/3600);
                                    $m = floor(($s%3600)/60);
                                    $sec = $s%60;
                                    echo $h.':'.str_pad($m,2,'0',STR_PAD_LEFT).':'.str_pad($sec,2,'0',STR_PAD_LEFT);
                                    ?>
                                    </span>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
