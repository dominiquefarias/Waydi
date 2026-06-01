<?php
function requireAdmin(PDO $pdo): int {
    $userId = requireAuth();
    $stmt   = $pdo->prepare('SELECT is_admin FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if (!$user || !$user['is_admin']) {
        http_response_code(403);
        echo json_encode(['error' => 'Acceso denegado']);
        exit;
    }
    return $userId;
}
