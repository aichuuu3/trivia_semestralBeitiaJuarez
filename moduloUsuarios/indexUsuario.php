<?php/*
session_start();
$nombreUsuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Admin';
*/?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - ReichMind</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/indexUsuario.css">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-star"></i>
            </div>
            <span>ReichMind</span>
        </div>
    </header>
        <?php
    session_start();
    if (!isset($_SESSION['rol'])) {
        exit;
    }
    ?>
    <div class="main-container">
        <!-- Sidebar -->
        <?php
        // Sidebar dinámico
        if ($_SESSION['rol'] === 'admin') {
            echo '<div class="sidebar">';
            include '../sidebar/sb.php';
            echo '</div>';
        } elseif ($_SESSION['rol'] === 'colaborador') {
            echo '<div class="sidebar">';
            include '../sidebar/sbcolaborador.php';
            echo '</div>';
        }
        ?> 

        <!-- Main Content -->
        <main class="main-content">
            <div class="container-fluid">
        <h1>🛍️ Sistema de Gestión de Usuarios</h1>
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="text-center">Registro de usuarios</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" id="frm">
                            <div class="form-group">
                                <label for="nombre">Nombre Usuario <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre completo" required minlength="2" maxlength="100">
                                <small class="form-text text-muted">Mínimo 2 caracteres</small>
                            </div>
                    
                            <div class="form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="correo@ejemplo.com" required>
                                <small class="form-text text-muted">Debe ser un email válido</small>
                            </div>
                    
                            <div class="form-group">
                                <label for="password">Contraseña <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Mínimo 6 caracteres" required minlength="6">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Mínimo 6 caracteres</small>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirmar contraseña <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Repetir contraseña" required minlength="6">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                            <i class="fas fa-eye" id="toggleConfirmPasswordIcon"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Debe coincidir con la contraseña</small>
                            </div>
                        
                            <div class="form-group">
                                <button type="submit" id="registrar" class="btn btn-primary btn-block">
                                    <i class="fas fa-user-plus"></i> Registrar Usuario
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-lg-6 ml-auto">
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="buscra">Buscar:</label>
                                <input type="text" name="buscar" id="buscar" placeholder="Buscar..." class="form-control">
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Contenedor con scroll horizontal -->
                <div class="table-responsive" style="max-height: 600px; overflow-x: auto; overflow-y: auto;">
                    <table class="table table-hover">
                        <thead class="thead-dark" style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th style="min-width: 50px;">ID</th>
                                <th style="min-width: 150px;">Nombre</th>
                                <th style="min-width: 200px;">Email</th>
                                <th style="min-width: 120px;">Contraseña</th>
                                <th style="min-width: 100px;">Monedas</th>
                                <th style="min-width: 120px;">Nivel</th>
                                <th style="min-width: 150px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="resultado">
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                    <p class="mt-2">Cargando usuarios...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
            </div>
        </main>
    </div>

    <script src="../js/scriptUsuarios.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
    <!-- Script para el menú lateral -->
    <script>
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.addEventListener('click', function(e) {
                // Si el enlace tiene href="#", prevenir la navegación
                if (this.getAttribute('href') === '#') {
                    e.preventDefault();
                }
                
                // Remover clase active de todos los enlaces
                document.querySelectorAll('.sidebar-menu a').forEach(l => l.classList.remove('active'));
                // Agregar clase active al enlace clickeado
                this.classList.add('active');
                
                const section = this.querySelector('span:last-child').textContent;
                console.log(`Navegando a: ${section}`);
            });
        });
    </script>
</body>

</html>