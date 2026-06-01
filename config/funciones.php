<?php
function sesion(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

function usuarioActual(): ?array {
    sesion();
    return $_SESSION['usuario'] ?? null;
}

function requireLogin(): array {
    sesion();
    if (empty($_SESSION['usuario'])) {
        header('Location: /login.php'); exit;
    }
    return $_SESSION['usuario'];
}

function requireAdmin(): array {
    $u = requireLogin();
    if (empty($u['is_admin'])) {
        header('Location: /index.php'); exit;
    }
    return $u;
}

function setFlash(string $tipo, string $msg): void {
    sesion();
    $_SESSION['flash'] = ['tipo' => $tipo, 'msg' => $msg];
}

function getFlash(): ?array {
    sesion();
    if (!isset($_SESSION['flash'])) return null;
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

// Escapa HTML de forma segura
function e(mixed $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// Envía correo usando la función mail() de PHP
function enviarCorreo(string $para, string $asunto, string $cuerpoHtml): bool {
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $from     = $_ENV['MAIL_FROM']      ?? 'noreply@waydi.local';
    $name     = $_ENV['MAIL_FROM_NAME'] ?? 'waydi';
    $headers .= "From: $name <$from>\r\n";
    return mail($para, $asunto, $cuerpoHtml, $headers);
}

// Convierte el valor de categoría de la BD al slug CSS (sin tildes, minúsculas)
function slugCategoria(string $cat): string {
    $map = [
        'gastronomía'    => 'gastronomia',
        'gastronomia'    => 'gastronomia',
        'cultura'        => 'cultura',
        'transporte'     => 'transporte',
        'naturaleza'     => 'naturaleza',
        'aventura'       => 'aventura',
        'compras'        => 'compras',
        'alojamiento'    => 'alojamiento',
        'entretenimiento'=> 'entretenimiento',
        'ocio'           => 'ocio',
    ];
    return $map[mb_strtolower($cat, 'UTF-8')] ?? 'default';
}

// Devuelve el emoji de una categoría (recibe el slug)
function iconoCategoria(string $slug): string {
    $iconos = [
        'gastronomia'    => '🍽️',
        'cultura'        => '🏛️',
        'transporte'     => '🚉',
        'naturaleza'     => '🌿',
        'aventura'       => '⛵',
        'compras'        => '🛍️',
        'alojamiento'    => '🏨',
        'entretenimiento'=> '🎭',
        'ocio'           => '🌅',
    ];
    return $iconos[$slug] ?? '📍';
}

// Clase CSS para la pastilla de categoría (recibe el slug)
function claseCategoria(string $slug): string {
    $clases = [
        'gastronomia'    => 'cat-gastronomia',
        'cultura'        => 'cat-cultura',
        'transporte'     => 'cat-transporte',
        'naturaleza'     => 'cat-naturaleza',
        'aventura'       => 'cat-aventura',
        'compras'        => 'cat-compras',
        'alojamiento'    => 'cat-alojamiento',
        'entretenimiento'=> 'cat-entretenimiento',
        'ocio'           => 'cat-ocio',
    ];
    return $clases[$slug] ?? 'cat-default';
}
