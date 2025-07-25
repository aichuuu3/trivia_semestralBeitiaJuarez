
<?php
session_start();

// Incluir la conexión a la base de datos
require_once '../bd/conexion.php';

$error_message = '';
$success_message = '';

// Procesar el formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validar que los campos no estén vacíos
    if (empty($email) || empty($password)) {
        $error_message = 'Por favor, complete todos los campos';
    } else {
        try {
            // Buscar el usuario por email
            $sql = "SELECT u.id, u.nombre, u.email, u.password, u.activo, u.cod_categoria, u.monedas_totales,
                           c.nombre_categoria
                    FROM usuarios u
                    LEFT JOIN categoria c ON u.cod_categoria = c.id_categoria
                    WHERE u.email = :email AND u.activo = 1";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario && password_verify($password, $usuario['password'])) {
                // Credenciales correctas - iniciar sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_categoria'] = $usuario['cod_categoria'];
                $_SESSION['usuario_monedas'] = $usuario['monedas_totales'];
                $_SESSION['usuario_nivel'] = $usuario['nombre_categoria'];
                
                $success_message = 'Inicio de sesión exitoso. Redirigiendo...';
                
                // Redirigir después de 2 segundos
                header("refresh:2;url=../panelControl/inicio.php");
            } else {
                $error_message = 'Email o contraseña incorrectos';
            }
            
        } catch (Exception $e) {
            $error_message = 'Error del servidor. Intente nuevamente.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../css/login.css">
    <script src="../js/ventanas.js"></script>
    <style>
        .alert {
            padding: 12px 16px;
            margin: 10px 0;
            border-radius: 5px;
            font-size: 14px;
            text-align: center;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .form-group input {
            border: 2px solid #ddd;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            border-color: #007bff;
            outline: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="form-title">Iniciar Sesión</h2>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error">
                <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <strong>Éxito:</strong> <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <div class="main-content">
            <div class="login-section">
                <form method="POST">
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <input type="password" name="password" placeholder="Contraseña" required>
                    </div>
                    
                    <button type="submit" name="login" class="submit-btn">Iniciar Sesión</button>
                </form>
            </div>
            
            <div class="additional-options">
                <p class="register-label">si no eres usuario, no puedes ingresar.</p>
                <div class="button-group">
                    <button type="button" name="logout" class="logout-btn" onclick="window.location.href='https://www.google.com'">Salir</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
