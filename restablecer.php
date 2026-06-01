<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/funciones.php';

$token   = trim($_GET['token'] ?? '');
$error   = '';
$exito   = false;

if (!$token) { header('Location: /login.php'); exit; }

$pdo  = getDB();
$stmt = $pdo->prepare('SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()');
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    $error = 'El enlace es inválido o ha expirado.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');

    if (strlen($password) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($password !== $confirm) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $pdo->prepare('UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?')
            ->execute([$hash, $user['id']]);
        $exito = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva contraseña · waydi</title>
    <link rel="stylesheet" href="/css/auth.css">
</head>
<body>
<div class="auth-card">
    <a href="/" class="auth-logo">
        <span class="logo-w">W</span>
        <span class="logo-txt">waydi</span>
    </a>

    <h1>Nueva contraseña</h1>
    <p class="subtitulo">Elige una contraseña segura</p>

    <?php if ($error): ?>
        <div class="alerta alerta-error"><?= e($error) ?></div>
        <div class="enlaces"><a href="/recuperar.php">Solicitar nuevo enlace</a></div>
    <?php elseif ($exito): ?>
        <div class="alerta alerta-exito">¡Contraseña actualizada! Ya puedes iniciar sesión.</div>
        <div class="enlaces"><a href="/login.php">Ir al inicio de sesión</a></div>
    <?php else: ?>
        <form method="POST">
            <div class="campo">
                <label for="password">Nueva contraseña (mín. 8 caracteres)</label>
                <input type="password" id="password" name="password" autocomplete="new-password" required>
            </div>
            <div class="campo">
                <label for="confirm">Confirmar contraseña</label>
                <input type="password" id="confirm" name="confirm" autocomplete="new-password" required>
            </div>
            <button type="submit" class="btn-primary">Guardar contraseña</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
