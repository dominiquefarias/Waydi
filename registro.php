<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/funciones.php';
sesion();

if (!empty($_SESSION['usuario'])) { header('Location: /index.php'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');

    if (!$name || !$email || !$password) {
        $error = 'Completa todos los campos.';
    } elseif (strlen($password) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($password !== $confirm) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Ya existe una cuenta con ese email.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, $hash]);
            $id = (int)$pdo->lastInsertId();

            session_regenerate_id(true);
            $_SESSION['usuario'] = ['id' => $id, 'name' => $name, 'email' => $email, 'is_admin' => 0];
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
    <title>Crear cuenta · waydi</title>
    <link rel="stylesheet" href="/css/auth.css">
</head>
<body>
<div class="auth-card">
    <a href="/" class="auth-logo">
        <span class="logo-w">W</span>
        <span class="logo-txt">waydi</span>
    </a>

    <h1>Crea tu cuenta</h1>
    <p class="subtitulo">Empieza a planificar tus viajes</p>

    <?php if ($error): ?>
        <div class="alerta alerta-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="campo">
            <label for="name">Nombre</label>
            <input type="text" id="name" name="name" value="<?= e($_POST['name'] ?? '') ?>" autocomplete="name" required>
        </div>
        <div class="campo">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" autocomplete="email" required>
        </div>
        <div class="campo">
            <label for="password">Contraseña (mín. 8 caracteres)</label>
            <input type="password" id="password" name="password" autocomplete="new-password" required>
        </div>
        <div class="campo">
            <label for="confirm">Confirmar contraseña</label>
            <input type="password" id="confirm" name="confirm" autocomplete="new-password" required>
        </div>
        <button type="submit" class="btn-primary">Crear cuenta</button>
    </form>

    <div class="enlaces">
        <span>¿Ya tienes cuenta? <a href="/login.php">Inicia sesión</a></span>
    </div>
</div>
</body>
</html>
