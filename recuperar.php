<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/funciones.php';

$enviado = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!$email) {
        $error = 'Introduce tu email.';
    } else {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT id, name FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $token   = bin2hex(random_bytes(32));
            $expira  = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $pdo->prepare('UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?')
                ->execute([$token, $expira, $user['id']]);

            $url  = ($_ENV['APP_URL'] ?? 'http://localhost') . "/restablecer.php?token=$token";
            $html = "<p>Hola {$user['name']},</p>
                     <p>Haz clic aquí para restablecer tu contraseña (válido 1 hora):</p>
                     <p><a href='$url'>$url</a></p>";

            enviarCorreo($email, 'Recuperar contraseña · waydi', $html);
        }

        // Respuesta genérica (no revela si el email existe)
        $enviado = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña · waydi</title>
    <link rel="stylesheet" href="/css/auth.css">
</head>
<body>
<div class="auth-card">
    <a href="/" class="auth-logo">
        <span class="logo-w">W</span>
        <span class="logo-txt">waydi</span>
    </a>

    <h1>Recuperar contraseña</h1>
    <p class="subtitulo">Te enviaremos un enlace por email</p>

    <?php if ($error): ?>
        <div class="alerta alerta-error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if ($enviado): ?>
        <div class="alerta alerta-exito">
            Si ese email está registrado, recibirás el enlace en breve.
        </div>
        <div class="enlaces">
            <a href="/login.php">← Volver al inicio de sesión</a>
        </div>
    <?php else: ?>
        <form method="POST">
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" autocomplete="email" required>
            </div>
            <button type="submit" class="btn-primary">Enviar enlace</button>
        </form>
        <div class="enlaces">
            <a href="/login.php">← Volver al inicio de sesión</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
