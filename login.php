<?php
require_once __DIR__ . '/config.php';

// Redireccionar si ya está logueado
if (isAdmin()) {
    header('Location: admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (strtolower($username) === 'admin') {
        try {
            $stmt = $pdo->prepare("SELECT value FROM settings WHERE key = 'admin_password'");
            $stmt->execute();
            $hash = $stmt->fetchColumn();

            if ($hash && password_verify($password, $hash)) {
                $_SESSION['admin_logged'] = true;
                header('Location: admin.php');
                exit;
            } else {
                $error = 'Contraseña incorrecta.';
            }
        } catch (PDOException $e) {
            $error = 'Error de base de datos: ' . $e->getMessage();
        }
    } else {
        $error = 'Usuario incorrecto (debe ser admin).';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - <?php echo htmlspecialchars($site_name); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">

    <div class="login-card">
        <div class="login-header">
            <h1><?php echo htmlspecialchars($site_name); ?></h1>
            <p>Panel de Administración</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="checkout-form">
            <div class="form-group">
                <label for="username" class="form-label">Usuario</label>
                <input type="text" name="username" id="username" required placeholder="admin" class="form-input" autofocus>
            </div>
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" name="password" id="password" required placeholder="••••••••••••" class="form-input">
            </div>

            <button type="submit" class="btn-primary" style="justify-content: center; width: 100%; padding: 0.85rem;">
                Entrar al Panel
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 1.5rem;">
            <a href="index.php" class="footer-admin-link">← Volver a la Tienda</a>
        </div>
    </div>

</body>
</html>
