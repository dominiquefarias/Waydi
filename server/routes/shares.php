<?php
function handleShares(string $path, string $method, PDO $pdo): void {
    $userId = requireAuth();
    $path   = trim($path, '/');
    $parts  = $path !== '' ? explode('/', $path) : [];

    // ── GET /api/invitations — invitaciones pendientes del usuario ──
    if (count($parts) === 1 && $parts[0] === 'invitations' && $method === 'GET') {
        $stmt = $pdo->prepare("
            SELECT ts.id, ts.role, ts.status, ts.created_at,
                   t.id AS trip_id, t.title, t.city, t.date_range,
                   u.name AS invited_by_name, u.email AS invited_by_email
            FROM trip_shares ts
            JOIN trips t ON t.id = ts.trip_id
            JOIN users u ON u.id = ts.invited_by
            WHERE ts.user_id = ? AND ts.status = 'pending'
            ORDER BY ts.created_at DESC
        ");
        $stmt->execute([$userId]);
        echo json_encode($stmt->fetchAll());
        return;
    }

    // ── POST /api/invitations/:shareId/accept ──────────────
    if (count($parts) === 3 && $parts[0] === 'invitations' && $parts[2] === 'accept' && $method === 'POST') {
        $shareId = (int)$parts[1];
        $stmt = $pdo->prepare('SELECT id FROM trip_shares WHERE id = ? AND user_id = ? AND status = "pending"');
        $stmt->execute([$shareId, $userId]);
        if (!$stmt->fetch()) { http_response_code(404); echo json_encode(['error' => 'Invitación no encontrada']); return; }
        $pdo->prepare('UPDATE trip_shares SET status = "accepted" WHERE id = ?')->execute([$shareId]);
        echo json_encode(['message' => 'Invitación aceptada']);
        return;
    }

    // ── POST /api/invitations/:shareId/decline ─────────────
    if (count($parts) === 3 && $parts[0] === 'invitations' && $parts[2] === 'decline' && $method === 'POST') {
        $shareId = (int)$parts[1];
        $stmt = $pdo->prepare('SELECT id FROM trip_shares WHERE id = ? AND user_id = ? AND status = "pending"');
        $stmt->execute([$shareId, $userId]);
        if (!$stmt->fetch()) { http_response_code(404); echo json_encode(['error' => 'Invitación no encontrada']); return; }
        $pdo->prepare('UPDATE trip_shares SET status = "declined" WHERE id = ?')->execute([$shareId]);
        echo json_encode(['message' => 'Invitación rechazada']);
        return;
    }

    // ── POST /api/trips/:tripId/share — invitar por email ──
    if (count($parts) === 2 && $parts[1] === 'share' && $method === 'POST') {
        $tripId = (int)$parts[0];
        $b      = json_decode(file_get_contents('php://input'), true) ?? [];
        $email  = trim($b['email'] ?? '');
        $role   = in_array($b['role'] ?? '', ['viewer','editor']) ? $b['role'] : 'editor';

        if (!$email) { http_response_code(400); echo json_encode(['error' => 'Email obligatorio']); return; }

        // Solo el dueño puede invitar
        $stmt = $pdo->prepare('SELECT id, title FROM trips WHERE id = ? AND user_id = ?');
        $stmt->execute([$tripId, $userId]);
        $trip = $stmt->fetch();
        if (!$trip) { http_response_code(403); echo json_encode(['error' => 'No tienes permiso para compartir este viaje']); return; }

        // No puedes invitarte a ti mismo
        $stmt = $pdo->prepare('SELECT email FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $owner = $stmt->fetch();
        if (strtolower($owner['email']) === strtolower($email)) {
            http_response_code(400); echo json_encode(['error' => 'No puedes invitarte a ti mismo']); return;
        }

        // El usuario invitado debe tener cuenta en waydi
        $stmt = $pdo->prepare('SELECT id, name FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $guest = $stmt->fetch();
        if (!$guest) { http_response_code(404); echo json_encode(['error' => 'No existe ninguna cuenta con ese email']); return; }

        // Evitar duplicados
        $stmt = $pdo->prepare('SELECT id, status FROM trip_shares WHERE trip_id = ? AND user_id = ?');
        $stmt->execute([$tripId, $guest['id']]);
        $existing = $stmt->fetch();
        if ($existing) {
            if ($existing['status'] === 'accepted') { http_response_code(409); echo json_encode(['error' => 'Este usuario ya tiene acceso al viaje']); return; }
            // Re-invitar si rechazó o está pendiente
            $pdo->prepare('UPDATE trip_shares SET role = ?, status = "pending", created_at = NOW() WHERE id = ?')
                ->execute([$role, $existing['id']]);
        } else {
            $pdo->prepare('INSERT INTO trip_shares (trip_id, user_id, invited_by, role) VALUES (?,?,?,?)')
                ->execute([$tripId, $guest['id'], $userId, $role]);
        }

        // Enviar email de invitación
        try {
            require_once __DIR__ . '/../mailer.php';
            $ownerStmt = $pdo->prepare('SELECT name FROM users WHERE id = ?');
            $ownerStmt->execute([$userId]);
            $ownerName = $ownerStmt->fetch()['name'];
            sendTripInvitation($email, $guest['name'], $ownerName, $trip['title'], $role);
        } catch (Exception $e) {
            error_log('Mailer error: ' . $e->getMessage());
        }

        echo json_encode(['message' => "Invitación enviada a {$guest['name']}"]);
        return;
    }

    // ── GET /api/trips/:tripId/members — ver colaboradores ─
    if (count($parts) === 2 && $parts[1] === 'members' && $method === 'GET') {
        $tripId = (int)$parts[0];

        // Dueño o colaborador aceptado pueden ver la lista
        $access = canAccessTrip($tripId, $userId, $pdo);
        if (!$access) { http_response_code(403); echo json_encode(['error' => 'Sin acceso']); return; }

        // Dueño
        $stmt = $pdo->prepare('SELECT u.id, u.name, u.email, "owner" AS role, "accepted" AS status FROM users u JOIN trips t ON t.user_id = u.id WHERE t.id = ?');
        $stmt->execute([$tripId]);
        $members = $stmt->fetchAll();

        // Colaboradores
        $stmt = $pdo->prepare("
            SELECT u.id, u.name, u.email, ts.role, ts.status
            FROM trip_shares ts JOIN users u ON u.id = ts.user_id
            WHERE ts.trip_id = ? AND ts.status != 'declined'
            ORDER BY ts.created_at
        ");
        $stmt->execute([$tripId]);
        $members = array_merge($members, $stmt->fetchAll());
        echo json_encode($members);
        return;
    }

    // ── DELETE /api/trips/:tripId/members/:memberId ─────────
    if (count($parts) === 3 && $parts[1] === 'members' && $method === 'DELETE') {
        $tripId   = (int)$parts[0];
        $memberId = (int)$parts[2];

        // Solo el dueño puede quitar colaboradores
        $stmt = $pdo->prepare('SELECT id FROM trips WHERE id = ? AND user_id = ?');
        $stmt->execute([$tripId, $userId]);
        if (!$stmt->fetch()) { http_response_code(403); echo json_encode(['error' => 'Solo el dueño puede quitar colaboradores']); return; }

        $pdo->prepare('DELETE FROM trip_shares WHERE trip_id = ? AND user_id = ?')->execute([$tripId, $memberId]);
        echo json_encode(['message' => 'Colaborador eliminado']);
        return;
    }

    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada']);
}

function canAccessTrip(int $tripId, int $userId, PDO $pdo): bool {
    // Es dueño
    $stmt = $pdo->prepare('SELECT id FROM trips WHERE id = ? AND user_id = ?');
    $stmt->execute([$tripId, $userId]);
    if ($stmt->fetch()) return true;

    // Es colaborador aceptado
    $stmt = $pdo->prepare('SELECT id FROM trip_shares WHERE trip_id = ? AND user_id = ? AND status = "accepted"');
    $stmt->execute([$tripId, $userId]);
    return (bool)$stmt->fetch();
}
