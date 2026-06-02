<?php
session_start();

define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'waydi2024');

function requireAuth(): void {
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: index.php');
        exit;
    }
}

function isLoggedIn(): bool {
    return !empty($_SESSION['admin_logged_in']);
}

function doLogin(string $username, string $password): bool {
    if ($username === ADMIN_USER && $password === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $username;
        return true;
    }
    return false;
}

function doLogout(): void {
    session_destroy();
    header('Location: index.php');
    exit;
}
