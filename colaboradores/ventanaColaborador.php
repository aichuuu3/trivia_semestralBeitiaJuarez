<?php /*
session_start();
// Verificar si el usuario está logueado como administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$nombreUsuario = $_SESSION['usuario'];*/
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - AhaSlides</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
         <nav class="sidebar">
            <ul class="sidebar-menu">
                <li>
                    <a href="../colaboradores/ventanaColaborador.php" class="active">
                        <span class="icon"><i class="fas fa-home"></i></span>
                        <span>Inicio</span>
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
                    <a href="../administrativos/configuracion.php">
                        <span class="icon"><i class="fas fa-cog"></i></span>
                        <span>Configuración</span>
                    </a>
                </li>
            </ul>
        </nav> 

        <!-- Main Content -->
        <main class="main-content">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h1 class="welcome-title">¡Hey Bienvenido <?php echo htmlspecialchars($nombreUsuario); ?> a ReichMind!</h1>
                <p class="welcome-subtitle">¡Ten cuidado con los cambios que hagas super adminsito!</p>
            </div>

            <!-- Mission and Vision Section -->
            <section class="mission-vision-section">
                <div class="mission-vision-container">
                    <div class="mission-card">
                        <div class="card-header">
                            <i class="fas fa-bullseye"></i>
                            <h3>Nuestra Misión</h3>
                        </div>
                        <p>Transformar la educación a través de tecnología innovadora, creando experiencias de aprendizaje interactivas que potencien el conocimiento y fomenten el crecimiento intelectual de nuestros usuarios.</p>
                    </div>
                    <div class="vision-card">
                        <div class="card-header">
                            <i class="fas fa-eye"></i>
                            <h3>Nuestra Visión</h3>
                        </div>
                        <p>Ser la plataforma líder en soluciones educativas digitales, reconocida por su excelencia, innovación y compromiso con el desarrollo del talento humano a nivel global.</p>
                    </div>
                </div>
            </section>

            <!-- Action Cards -->
            <div class="action-cards">
                <a href="#" class="action-card highlighted">
                    <div class="card-icon blank">
                        <i class="fas fa-file-blank"></i>
                    </div>
                    <div class="card-title">Usuarios</div>
                    <div class="card-description">Total de usuarios</div>
                </a>

                <a href="#" class="action-card">
                    <div class="card-icon quiz">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div class="card-title">Categorias</div>
                    <div class="card-description">Total de categorias</div>
                </a>

                <a href="#" class="action-card">
                    <div class="card-icon poll">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="card-title">Preguntas</div>
                    <div class="card-description">Total de preguntas</div>
                </a>
            </div>
        </main>
    </div>

    <script>
        // Script para hacer funcional la búsqueda
        document.querySelector('.search-bar input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                alert('Función de búsqueda en desarrollo');
            }
        });

        // Script para los clicks en las tarjetas de acción
        document.querySelectorAll('.action-card').forEach(card => {
            card.addEventListener('click', function(e) {
                e.preventDefault();
                const title = this.querySelector('.card-title').textContent;
                alert(`Creando nueva presentación: ${title}`);
            });
        });

        // Script para el menú lateral
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
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
