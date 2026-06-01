<?php
function getDB(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;

    $host = $_ENV['DB_HOST']     ?? 'localhost';
    $port = $_ENV['DB_PORT']     ?? '3306';
    $name = $_ENV['DB_NAME']     ?? 'waydi';
    $user = $_ENV['DB_USER']     ?? 'root';
    $pass = $_ENV['DB_PASSWORD'] ?? '';

    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4",
        $user, $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    return $pdo;
}
