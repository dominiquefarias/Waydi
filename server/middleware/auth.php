<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function requireAuth(): int {
    $headers = getallheaders();
    $header  = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (!str_starts_with($header, 'Bearer ')) {
        http_response_code(401);
        echo json_encode(['error' => 'Token requerido']);
        exit;
    }

    $token = substr($header, 7);
    try {
        $payload = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        return (int) $payload->userId;
    } catch (Exception) {
        http_response_code(401);
        echo json_encode(['error' => 'Token inválido o expirado']);
        exit;
    }
}
