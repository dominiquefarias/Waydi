<?php
use Firebase\JWT\JWT;

function handleAuth(string $action, string $method, PDO $pdo): void {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];

    // ── POST /api/auth/register ────────────────────────────
    if ($action === 'register' && $method === 'POST') {
        $name     = trim($body['name']     ?? '');
        $email    = trim($body['email']    ?? '');
        $password = trim($body['password'] ?? '');

        if (!$name || !$email || !$password) {
            http_response_code(400);
            echo json_encode(['error' => 'Nombre, email y contraseña son obligatorios']);
            return;
        }
        if (strlen($password) < 8) {
            http_response_code(400);
            echo json_encode(['error' => 'La contraseña debe tener al menos 8 caracteres']);
            return;
        }

        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'Ya existe una cuenta con ese email']);
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $hash]);
        $id    = (int) $pdo->lastInsertId();
        $token = makeJWT($id);

        http_response_code(201);
        echo json_encode(['token' => $token, 'user' => ['id' => $id, 'name' => $name, 'email' => $email]]);
        return;
    }

    // ── POST /api/auth/login ───────────────────────────────
    if ($action === 'login' && $method === 'POST') {
        $email    = trim($body['email']    ?? '');
        $password = trim($body['password'] ?? '');

        if (!$email || !$password) {
            http_response_code(400);
            echo json_encode(['error' => 'Email y contraseña son obligatorios']);
            return;
        }

        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Email o contraseña incorrectos']);
            return;
        }

        $token = makeJWT((int)$user['id']);
        echo json_encode([
            'token' => $token,
            'user'  => ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email']],
        ]);
        return;
    }

    // ── GET /api/auth/me ───────────────────────────────────
    if ($action === 'me' && $method === 'GET') {
        $userId = requireAuth();
        $stmt   = $pdo->prepare('SELECT id, name, email, is_admin, created_at FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if (!$user) { http_response_code(404); echo json_encode(['error' => 'Usuario no encontrado']); return; }
        echo json_encode($user);
        return;
    }

    // ── POST /api/auth/forgot-password ────────────────────
    if ($action === 'forgot-password' && $method === 'POST') {
        $email = trim($body['email'] ?? '');
        if (!$email) { http_response_code(400); echo json_encode(['error' => 'Email obligatorio']); return; }

        $stmt = $pdo->prepare('SELECT id, name FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Respuesta genérica para no revelar si el email existe
        if ($user) {
            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $pdo->prepare('UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?')
                ->execute([$token, $expires, $user['id']]);

            $resetUrl = ($_ENV['FRONTEND_URL'] ?? 'http://localhost:5173') . "/reset-password?token=$token";
            try {
                require_once __DIR__ . '/../mailer.php';
                sendPasswordReset($email, $user['name'], $resetUrl);
            } catch (Exception $e) {
                error_log('Mailer error: ' . $e->getMessage());
            }
        }

        echo json_encode(['message' => 'Si ese email está registrado, recibirás un enlace en breve.']);
        return;
    }

    // ── POST /api/auth/reset-password ─────────────────────
    if ($action === 'reset-password' && $method === 'POST') {
        $token    = trim($body['token']    ?? '');
        $password = trim($body['password'] ?? '');

        if (!$token || !$password) {
            http_response_code(400);
            echo json_encode(['error' => 'Token y nueva contraseña son obligatorios']);
            return;
        }
        if (strlen($password) < 8) {
            http_response_code(400);
            echo json_encode(['error' => 'La contraseña debe tener al menos 8 caracteres']);
            return;
        }

        $stmt = $pdo->prepare('SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()');
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) {
            http_response_code(400);
            echo json_encode(['error' => 'El enlace es inválido o ha expirado']);
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
        $pdo->prepare('UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?')
            ->execute([$hash, $user['id']]);

        echo json_encode(['message' => 'Contraseña actualizada correctamente']);
        return;
    }

    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada']);
}

function makeJWT(int $userId): string {
    $payload = ['userId' => $userId, 'exp' => time() + 7 * 24 * 3600];
    return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
}
