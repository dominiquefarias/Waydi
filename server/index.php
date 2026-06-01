<?php
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/middleware/auth.php';

// ── CORS ──────────────────────────────────────────────────
$origin = $_ENV['FRONTEND_URL'] ?? 'http://localhost:5173';
header("Access-Control-Allow-Origin: $origin");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ── Router ────────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim($uri, '/');

try {
    $pdo = getDB();

    if (preg_match('#^/api/auth/([^/]+)$#', $uri, $m)) {
        require_once __DIR__ . '/routes/auth.php';
        handleAuth($m[1], $method, $pdo);

    } elseif (preg_match('#^/api/trips(/.*)?$#', $uri, $m)) {
        require_once __DIR__ . '/routes/trips.php';
        handleTrips($m[1] ?? '', $method, $pdo);

    } elseif ($uri === '/api/health') {
        echo json_encode(['ok' => true, 'service' => 'waydi API (PHP)']);

    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Ruta no encontrada']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de base de datos']);
    error_log('PDO: ' . $e->getMessage());
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor']);
    error_log('Error: ' . $e->getMessage());
}
