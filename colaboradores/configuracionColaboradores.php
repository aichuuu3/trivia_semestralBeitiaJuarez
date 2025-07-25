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
    if ($nombre && $email) {
        $db->actualizarColaborador($colaborador_id, $nombre, $email, $password);
    }
    // Redirigir para evitar reenvío
    header('Location: configuracionColaboradores.php?tab=edit');
    exit();
}

// Obtener datos del colaborador
$colaborador = $db->obtenerColaborador($colaborador_id);
if (!$colaborador) {
    die('Colaborador no encontrado');
}

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
                        <img src="../img/colaborador_default.png" alt="Foto de perfil" />
                    </div>
                    <h2 class="profile-name"><?php echo htmlspecialchars($colaborador['nombre']); ?></h2>
                    <div class="profile-username">Colaborador ID: <?php echo $colaborador['id']; ?>
                        <?php if($tab=='edit'): ?> <i class="fas fa-pen edit-icon"></i><?php endif; ?>
                    </div>
                    <div class="profile-info-list">
                        <form id="form-editar-colaborador" method="post" action="">
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
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
