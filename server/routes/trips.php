<?php
function handleTrips(string $path, string $method, PDO $pdo): void {
    $userId = requireAuth();
    $path   = trim($path, '/');
    $parts  = $path !== '' ? explode('/', $path) : [];

    // ── GET /api/trips  ·  POST /api/trips ────────────────
    if (count($parts) === 0) {
        if ($method === 'GET') {
            // Viajes propios + viajes compartidos aceptados
            $stmt = $pdo->prepare("
                SELECT t.*, 'owner' AS my_role
                FROM trips t
                WHERE t.user_id = ?
                UNION
                SELECT t.*, ts.role AS my_role
                FROM trips t
                JOIN trip_shares ts ON ts.trip_id = t.id
                WHERE ts.user_id = ? AND ts.status = 'accepted'
                ORDER BY created_at DESC
            ");
            $stmt->execute([$userId, $userId]);
            echo json_encode($stmt->fetchAll());
        } elseif ($method === 'POST') {
            $b = json_decode(file_get_contents('php://input'), true) ?? [];
            if (empty($b['title'])) { http_response_code(400); echo json_encode(['error' => 'El título es obligatorio']); return; }
            $stmt = $pdo->prepare('INSERT INTO trips (user_id, title, subtitle, date_range, city, travelers) VALUES (?,?,?,?,?,?)');
            $stmt->execute([$userId, $b['title'], $b['subtitle'] ?? null, $b['date_range'] ?? null, $b['city'] ?? null, $b['travelers'] ?? 1]);
            $id = $pdo->lastInsertId();
            $stmt = $pdo->prepare('SELECT * FROM trips WHERE id = ?');
            $stmt->execute([$id]);
            http_response_code(201);
            echo json_encode($stmt->fetch());
        }
        return;
    }

    $tripId = (int)$parts[0];

    // Verificar acceso: dueño O colaborador aceptado
    require_once __DIR__ . '/shares.php';
    $isOwner = (bool) $pdo->prepare('SELECT id FROM trips WHERE id = ? AND user_id = ?')
        ->execute([$tripId, $userId]) && ($pdo->query("SELECT id FROM trips WHERE id=$tripId AND user_id=$userId")->fetch());
    // Forma limpia:
    $ownerStmt = $pdo->prepare('SELECT id FROM trips WHERE id = ? AND user_id = ?');
    $ownerStmt->execute([$tripId, $userId]);
    $isOwner = (bool)$ownerStmt->fetch();

    if (!$isOwner && !canAccessTrip($tripId, $userId, $pdo)) {
        http_response_code(404); echo json_encode(['error' => 'Viaje no encontrado']); return;
    }

    // ── GET /api/trips/:id  ·  PUT  ·  DELETE ─────────────
    if (count($parts) === 1) {
        if ($method === 'GET') {
            $stmt = $pdo->prepare('SELECT * FROM trips WHERE id = ?');
            $stmt->execute([$tripId]);
            $trip = $stmt->fetch();

            $dStmt = $pdo->prepare('SELECT * FROM days WHERE trip_id = ? ORDER BY position');
            $dStmt->execute([$tripId]);
            $days = $dStmt->fetchAll();

            foreach ($days as &$day) {
                $aStmt = $pdo->prepare('SELECT * FROM activities WHERE day_id = ? ORDER BY position');
                $aStmt->execute([$day['id']]);
                $day['activities'] = $aStmt->fetchAll();

                $nStmt = $pdo->prepare('SELECT * FROM notes WHERE day_id = ? ORDER BY position');
                $nStmt->execute([$day['id']]);
                $day['notes'] = $nStmt->fetchAll();
            }

            $trip['days'] = $days;
            echo json_encode($trip);

        } elseif ($method === 'PUT') {
            if (!$isOwner) { http_response_code(403); echo json_encode(['error' => 'Solo el dueño puede editar el viaje']); return; }
            $b = json_decode(file_get_contents('php://input'), true) ?? [];
            $pdo->prepare('UPDATE trips SET title=?, subtitle=?, date_range=?, city=?, travelers=? WHERE id=?')
                ->execute([$b['title'] ?? null, $b['subtitle'] ?? null, $b['date_range'] ?? null, $b['city'] ?? null, $b['travelers'] ?? 1, $tripId]);
            echo json_encode(['message' => 'Viaje actualizado']);

        } elseif ($method === 'DELETE') {
            if (!$isOwner) { http_response_code(403); echo json_encode(['error' => 'Solo el dueño puede eliminar el viaje']); return; }
            $pdo->prepare('DELETE FROM trips WHERE id = ?')->execute([$tripId]);
            echo json_encode(['message' => 'Viaje eliminado']);
        }
        return;
    }

    // ── POST /api/trips/:id/days ───────────────────────────
    if (count($parts) === 2 && $parts[1] === 'days' && $method === 'POST') {
        $b = json_decode(file_get_contents('php://input'), true) ?? [];
        $pdo->prepare('INSERT INTO days (trip_id, position, label, weekday, date, theme) VALUES (?,?,?,?,?,?)')
            ->execute([$tripId, $b['position'] ?? 1, $b['label'] ?? null, $b['weekday'] ?? null, $b['date'] ?? null, $b['theme'] ?? null]);
        $id = $pdo->lastInsertId();
        $stmt = $pdo->prepare('SELECT * FROM days WHERE id = ?');
        $stmt->execute([$id]);
        http_response_code(201);
        echo json_encode($stmt->fetch());
        return;
    }

    $dayId = isset($parts[2]) ? (int)$parts[2] : null;

    // ── POST /api/trips/:id/days/:dayId/activities ─────────
    if (count($parts) === 4 && $parts[3] === 'activities' && $method === 'POST') {
        $b = json_decode(file_get_contents('php://input'), true) ?? [];
        if (empty($b['name'])) { http_response_code(400); echo json_encode(['error' => 'El nombre es obligatorio']); return; }
        $pdo->prepare('INSERT INTO activities (day_id, position, time, duration, name, place, category, icon, pin_x, pin_y) VALUES (?,?,?,?,?,?,?,?,?,?)')
            ->execute([$dayId, $b['position'] ?? 1, $b['time'] ?? null, $b['duration'] ?? null, $b['name'], $b['place'] ?? null, $b['category'] ?? null, $b['icon'] ?? null, $b['pin_x'] ?? null, $b['pin_y'] ?? null]);
        $id = $pdo->lastInsertId();
        $stmt = $pdo->prepare('SELECT * FROM activities WHERE id = ?');
        $stmt->execute([$id]);
        http_response_code(201);
        echo json_encode($stmt->fetch());
        return;
    }

    $actId = isset($parts[4]) ? (int)$parts[4] : null;

    // ── PUT /api/trips/:id/days/:dayId/activities/:actId ───
    if (count($parts) === 5 && $method === 'PUT') {
        $b = json_decode(file_get_contents('php://input'), true) ?? [];
        $pdo->prepare('UPDATE activities SET time=?, duration=?, name=?, place=?, category=?, icon=?, pin_x=?, pin_y=?, position=? WHERE id=? AND day_id=?')
            ->execute([$b['time'] ?? null, $b['duration'] ?? null, $b['name'] ?? null, $b['place'] ?? null, $b['category'] ?? null, $b['icon'] ?? null, $b['pin_x'] ?? null, $b['pin_y'] ?? null, $b['position'] ?? 1, $actId, $dayId]);
        echo json_encode(['message' => 'Actividad actualizada']);
        return;
    }

    // ── DELETE /api/trips/:id/days/:dayId/activities/:actId ─
    if (count($parts) === 5 && $method === 'DELETE') {
        $pdo->prepare('DELETE FROM activities WHERE id = ? AND day_id = ?')->execute([$actId, $dayId]);
        echo json_encode(['message' => 'Actividad eliminada']);
        return;
    }

    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada']);
}
