<?php
session_start();
$nombreUsuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Admin';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Colaboradores - ReichMind</title>
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

    <div class="main-container">
        <!-- Sidebar -->
        <!-- Sidebar -->
        <nav class="sidebar">
            <ul class="sidebar-menu">
                <li>
                    <a href="../administrativos/ventanaAdmin.php" >
                        <span class="icon"><i class="fas fa-home"></i></span>
                        <span>Inicio</span>
                    </a>
                </li>
                <li>
                    <a href="../moduloUsuarios/indexUsuario.php">
                        <span class="icon"><i class="fas fa-users"></i></span>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="../moduloTemas/indexTema.php">
                        <span class="icon"><i class="fas fa-file-alt"></i></span>
                        <span>Categorias</span>
                    </a>
                </li>
                <li>
                    <a href="../moduloPreguntas/indexPreguntas.php">
                        <span class="icon"><i class="fas fa-question-circle"></i></span>
                        <span>Preguntas</span>
                    </a>
                </li>
                
                <li>
                    <a href="../adminColaboradores/indexColaborador.php" class="active">
                        <span class="icon"><i class="fas fa-plus"></i></span>
                        <span>Administrar Colaboradores</span>
                    </a>
                </li>
                <li>
                    <a href="../administrativos/configuracion.php">
                        <span class="icon"><i class="fas fa-cog"></i></span>
                        <span>Configuración</span>
                    </a>
                </li>
            </ul>
        </nav> 

        <!-- Main Content -->
        <main class="main-content">
            <div class="container-fluid">
                <h1> Sistema de Gestión de Colaboradores</h1>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-primary">
                                <h3 class="text-center" id="formTitle">Registro de Colaboradores</h3>
                            </div>
                            <div class="card-body">
                                <form action="" method="post" id="frm">
                                    <input type="hidden" name="id" id="colaboradorId">
                                    
                                    <div class="form-group">
                                        <label for="nombre">Nombre Colaborador</label>
                                        <input type="text" name="nombre" id="nombre" placeholder="Nombre completo" class="form-control" required>
                                    </div>
                            
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" id="email" placeholder="Correo electrónico" class="form-control" required>
                                    </div>
                            
                                    <div class="form-group">
                                        <label for="password">Contraseña</label>
                                        <input type="password" name="password" id="password" placeholder="Contraseña (mín. 6 caracteres)" class="form-control" required>
                                        <small class="form-text text-muted">Dejar vacío para mantener la contraseña actual (solo edición)</small>
                                    </div>
                                
                                    <div class="form-group">
                                        <input type="button" value="Registrar" id="registrar" class="btn btn-primary btn-block">
                                        <input type="button" value="Cancelar" id="cancelar" class="btn btn-secondary btn-block" style="display:none;">
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
                                        <label for="buscar">Buscar:</label>
                                        <input type="text" name="buscar" id="buscar" placeholder="Buscar colaboradores..." class="form-control">
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive" style="overflow-x: auto;">
                            <table class="table table-hover table-scroll">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Fecha Registro</th>
                                        <th>Hora Inicio</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="resultado">
                                    <!-- Los datos se cargan aquí dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="../js/registroColaboradores.js"></script>
</body>
</html>