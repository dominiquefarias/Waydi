<?php
function handleAdmin(string $path, string $method, PDO $pdo): void {
    require_once __DIR__ . '/../middleware/admin.php';
    requireAdmin($pdo);

    $path  = trim($path, '/');
    $parts = $path !== '' ? explode('/', $path) : [];

    // ── GET /api/admin/stats ──────────────────────────────
    if ($path === 'stats' && $method === 'GET') {
        $stats = [];

        $stats['total_users']     = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $stats['total_trips']     = $pdo->query('SELECT COUNT(*) FROM trips')->fetchColumn();
        $stats['total_activities']= $pdo->query('SELECT COUNT(*) FROM activities')->fetchColumn();
        $stats['total_shares']    = $pdo->query('SELECT COUNT(*) FROM trip_shares WHERE status = "accepted"')->fetchColumn();

        $stats['new_users_week']  = $pdo->query('SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)')->fetchColumn();
        $stats['new_trips_week']  = $pdo->query('SELECT COUNT(*) FROM trips WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)')->fetchColumn();

        // Últimos 7 días de registros (para gráfica)
        $stmt = $pdo->query("
            SELECT DATE(created_at) AS day, COUNT(*) AS count
            FROM users
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at) ORDER BY day
        ");
        $stats['registrations_chart'] = $stmt->fetchAll();

        // Distribución de categorías de actividades
        $stmt = $pdo->query('SELECT category, COUNT(*) AS count FROM activities GROUP BY category ORDER BY count DESC');
        $stats['categories'] = $stmt->fetchAll();

        echo json_encode($stats);
        return;
    }

    // ── GET /api/admin/users ──────────────────────────────
    if ($path === 'users' && $method === 'GET') {
        $search = $_GET['q'] ?? '';
        if ($search) {
            $stmt = $pdo->prepare("
                SELECT u.id, u.name, u.email, u.is_admin, u.created_at,
                       COUNT(DISTINCT t.id) AS total_trips
                FROM users u
                LEFT JOIN trips t ON t.user_id = u.id
                WHERE u.name LIKE ? OR u.email LIKE ?
                GROUP BY u.id ORDER BY u.created_at DESC
            ");
            $like = "%$search%";
            $stmt->execute([$like, $like]);
        } else {
            $stmt = $pdo->query("
                SELECT u.id, u.name, u.email, u.is_admin, u.created_at,
                       COUNT(DISTINCT t.id) AS total_trips
                FROM users u
                LEFT JOIN trips t ON t.user_id = u.id
                GROUP BY u.id ORDER BY u.created_at DESC
            ");
        }
        echo json_encode($stmt->fetchAll());
        return;
    }

    // ── GET /api/admin/users/:id ──────────────────────────
    if (count($parts) === 2 && $parts[0] === 'users' && $method === 'GET') {
        $uid  = (int)$parts[1];
        $stmt = $pdo->prepare('SELECT id, name, email, is_admin, created_at FROM users WHERE id = ?');
        $stmt->execute([$uid]);
        $user = $stmt->fetch();
        if (!$user) { http_response_code(404); echo json_encode(['error' => 'Usuario no encontrado']); return; }

        $stmt = $pdo->prepare('SELECT id, title, city, date_range, travelers, created_at FROM trips WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$uid]);
        $user['trips'] = $stmt->fetchAll();

        echo json_encode($user);
        return;
    }

    // ── PUT /api/admin/users/:id ──────────────────────────
    if (count($parts) === 2 && $parts[0] === 'users' && $method === 'PUT') {
        $uid = (int)$parts[1];
        $b   = json_decode(file_get_contents('php://input'), true) ?? [];

        $fields = [];
        $values = [];
        if (isset($b['name']))     { $fields[] = 'name = ?';     $values[] = $b['name']; }
        if (isset($b['email']))    { $fields[] = 'email = ?';    $values[] = $b['email']; }
        if (isset($b['is_admin'])) { $fields[] = 'is_admin = ?'; $values[] = (int)$b['is_admin']; }

        if (empty($fields)) { http_response_code(400); echo json_encode(['error' => 'Nada que actualizar']); return; }

        $values[] = $uid;
        $pdo->prepare('UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?')->execute($values);
        echo json_encode(['message' => 'Usuario actualizado']);
        return;
    }

    // ── DELETE /api/admin/users/:id ───────────────────────
    if (count($parts) === 2 && $parts[0] === 'users' && $method === 'DELETE') {
        $uid = (int)$parts[1];
        $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$uid]);
        echo json_encode(['message' => 'Usuario eliminado']);
        return;
    }

    // ── GET /api/admin/trips ──────────────────────────────
    if ($path === 'trips' && $method === 'GET') {
        $search = $_GET['q'] ?? '';
        if ($search) {
            $stmt = $pdo->prepare("
                SELECT t.id, t.title, t.city, t.date_range, t.travelers, t.created_at,
                       u.name AS owner_name, u.email AS owner_email,
                       COUNT(DISTINCT d.id) AS total_days,
                       COUNT(DISTINCT a.id) AS total_activities,
                       COUNT(DISTINCT ts.id) AS total_collaborators
                FROM trips t
                JOIN users u ON u.id = t.user_id
                LEFT JOIN days d ON d.trip_id = t.id
                LEFT JOIN activities a ON a.day_id = d.id
                LEFT JOIN trip_shares ts ON ts.trip_id = t.id AND ts.status = 'accepted'
                WHERE t.title LIKE ? OR t.city LIKE ? OR u.name LIKE ?
                GROUP BY t.id ORDER BY t.created_at DESC
            ");
            $like = "%$search%";
            $stmt->execute([$like, $like, $like]);
        } else {
            $stmt = $pdo->query("
                SELECT t.id, t.title, t.city, t.date_range, t.travelers, t.created_at,
                       u.name AS owner_name, u.email AS owner_email,
                       COUNT(DISTINCT d.id) AS total_days,
                       COUNT(DISTINCT a.id) AS total_activities,
                       COUNT(DISTINCT ts.id) AS total_collaborators
                FROM trips t
                JOIN users u ON u.id = t.user_id
                LEFT JOIN days d ON d.trip_id = t.id
                LEFT JOIN activities a ON a.day_id = d.id
                LEFT JOIN trip_shares ts ON ts.trip_id = t.id AND ts.status = 'accepted'
                GROUP BY t.id ORDER BY t.created_at DESC
            ");
        }
        echo json_encode($stmt->fetchAll());
        return;
    }

    // ── DELETE /api/admin/trips/:id ───────────────────────
    if (count($parts) === 2 && $parts[0] === 'trips' && $method === 'DELETE') {
        $tripId = (int)$parts[1];
        $pdo->prepare('DELETE FROM trips WHERE id = ?')->execute([$tripId]);
        echo json_encode(['message' => 'Viaje eliminado']);
        return;
    }

    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada']);
}
