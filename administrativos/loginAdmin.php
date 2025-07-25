<?php
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../bd/claseBD.php';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $db = new DB();
    $pdo = $db->getPdo();
    $stmt = $pdo->prepare('SELECT * FROM administradores WHERE email = ? AND activo = 1');
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($admin && password_verify($password, $admin['password'])) {
        session_start();
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nombre'] = $admin['nombre'];
        header('Location: ventanaAdmin.php');
        exit;
    } else {
        $loginError = 'Correo o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/loginAdmin.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-bg-gradient"></div>
    <div class="login-container">
        <div class="login-card">
            <div class="login-content">
                <form class="login-form" method="post" action="">
                    <?php if (!empty($loginError)): ?>
                        <div style="color:#ff4d6d;font-size:1rem;text-align:center;margin-bottom:8px;">
                            <?php echo $loginError; ?>
                        </div>
                    <?php endif; ?>
                    <div class="login-avatar">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="login-title">
                        <span class="welcome">Bienvenido a </span><span class="back">ReichMind</span>
                    </div>
                    <div class="login-fields">
                        <input type="email" id="email" name="email" placeholder="Correo institucional" required autocomplete="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                        <div id="email-feedback" style="color:#ff4d6d;font-size:0.98rem;margin-bottom:2px;min-height:18px;"></div>
                        <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
                    </div>
                    <button type="submit" class="login-btn">Log In</button>
                    <div class="login-remember">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                </form>
            </div>
        </div>
    </div>
</form>
<script>
// Evita submit si el correo no es válido
document.querySelector('.login-form').addEventListener('submit', function(e) {
    if (!lastEmailValid) {
        feedback.textContent = 'El correo no es válido o no está registrado.';
        emailInput.focus();
        e.preventDefault();
    }
});
// Validación JS para correo institucional ReichMind
const emailInput = document.getElementById('email');
const feedback = document.getElementById('email-feedback');
let lastEmailChecked = '';
let lastEmailValid = false;

function validateEmailFormat(email) {
    // nombre.apellido o nombre.apellido1,2,3... @reichmind.com
    const regex = /^[a-zA-Z]+\.[a-zA-Z]+\d{0,2}@reichmind\.com$/;
    return regex.test(email);
}

async function checkEmailExists(email) {
    // AJAX a PHP para validar existencia
    const res = await fetch('validarAdminEmail.php?email=' + encodeURIComponent(email));
    const data = await res.json();
    return data.exists;
}

emailInput.addEventListener('input', async function() {
    const email = emailInput.value.trim();
    if (!email) {
        feedback.textContent = '';
        emailInput.style.borderColor = '';
        return;
    }
    if (!validateEmailFormat(email)) {
        feedback.textContent = 'El correo debe ser nombre.apellido@reichmind.com';
        emailInput.style.borderColor = '#ff4d6d';
        lastEmailValid = false;
        return;
    }
    feedback.textContent = 'Verificando correo...';
    emailInput.style.borderColor = '#fbbf24';
    lastEmailChecked = email;
    const exists = await checkEmailExists(email);
    if (email !== lastEmailChecked) return; // Evita race conditions
    if (!exists) {
        feedback.textContent = 'Este correo no está registrado como administrativo.';
        emailInput.style.borderColor = '#ff4d6d';
        lastEmailValid = false;
    } else {
        feedback.textContent = 'Correo válido ✔';
        emailInput.style.borderColor = '#22c55e';
        lastEmailValid = true;
    }
});
</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>
