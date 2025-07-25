
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../css/login.css">
    <script src="../js/ventanas.js"></script>
</head>
<body>
    <div class="container">
        <h2 class="form-title">Iniciar Sesión</h2>
        
        <div class="main-content">
            <div class="login-section">
                <form method="POST">
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    
                    <div class="form-group">
                        <input type="password" name="password" placeholder="Contraseña" required>
                    </div>
                    
                    <button type="submit" name="login" class="submit-btn">Iniciar Sesión</button>
                </form>
            </div>
            
            <div class="additional-options">
                <p class="register-label">¿No quieres entrar?, pos salte</p>
                <div class="button-group">
                    <button type="button" name="logout" class="logout-btn" onclick="window.location.href='https://www.google.com'">Salir</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
