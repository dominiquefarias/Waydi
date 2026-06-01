<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/funciones.php';
sesion();

// Si ya está logueado, ir al inicio
if (!empty($_SESSION['usuario'])) { header('Location: /index.php'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $error = 'Completa todos los campos.';
    } else {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'Email o contraseña incorrectos.';
        } else {
            session_regenerate_id(true);
            $_SESSION['usuario'] = [
                'id'       => $user['id'],
                'name'     => $user['name'],
                'email'    => $user['email'],
                'is_admin' => (int)$user['is_admin'],
            ];
            header('Location: /index.php'); exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión · waydi</title>
    <link rel="stylesheet" href="/css/auth.css">
</head>
<body>
<div class="auth-card">
    <a href="/" class="auth-logo">
        <span class="logo-w">W</span>
        <span class="logo-txt">waydi</span>
    </a>

    <h1>Bienvenido de vuelta</h1>
    <p class="subtitulo">Inicia sesión para ver tus viajes</p>

    <?php if ($error): ?>
        <div class="alerta alerta-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="campo">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" autocomplete="email" required>
        </div>
        <div class="campo">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>
        </div>
        <button type="submit" class="btn-primary">Entrar</button>
    </form>

    <div class="enlaces">
        <span>¿No tienes cuenta? <a href="/registro.php">Regístrate</a></span>
        <a href="/recuperar.php">Olvidé mi contraseña</a>
    </div>
</div>
</body>
</html>
