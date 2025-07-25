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
    <link rel="stylesheet" href="styles.css">
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
                    <a href="../moduloUsuarios/indexUsuario.php" >
                        <span class="icon"><i class="fas fa-users"></i></span>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="../moduloTemas/indexTema.php" class="active">
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
                    <a href="#">
                        <span class="icon"><i class="fas fa-calendar"></i></span>
                        <span>Mensajes y notificaciones</span>
                    </a>
                </li>
                <li>
                    <a href="../adminColaboradores/indexColaborador.php">
                        <span class="icon"><i class="fas fa-plus"></i></span>
                        <span>Administrar Colaboradores</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="icon"><i class="fas fa-cog"></i></span>
                        <span>Configuración</span>
                    </a>
                </li>
            </ul>
        </nav> 

        <!-- Main Content -->
        <main class="main-content">
            <div class="container-fluid">
        <h1>🛍️ Sistema de Gestión de Temas</h1>
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="text-center">Registro de Temas</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" id="frm">
                            <div class="form-group">
                                <label for="">Categoría</label>
                                <input type="text" name="Categoria" id="nombre_tema" placeholder="Nombre categoría" required>
                            </div>
                            <div class="form-group">
                                <label for="">Descripción</label>
                                <textarea name="descripcion" id="descripcion" class="form-control" placeholder="Descripción del tema" required></textarea>
                            </div>
                            <div class="form-group">
                                <input type="button" value="Registrar" id="registrar" class="btn btn-primary btn-block">
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
                <table class="table table-hover table-resposive">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody id="resultado">

                    </tbody>
                </table>
            </div>
        </div>
            </div>
        </main>
    </div>

    <script src="../js/registroTemas.js"></script>
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