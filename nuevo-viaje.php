<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/funciones.php';

$usuario = requireLogin();
$pdo     = getDB();
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title      = trim($_POST['title']      ?? '');
    $subtitle   = trim($_POST['subtitle']   ?? '');
    $city       = trim($_POST['city']       ?? '');
    $date_range = trim($_POST['date_range'] ?? '');
    $travelers  = (int)($_POST['travelers'] ?? 1);

    if (!$title || !$city) {
        $error = 'El título y la ciudad son obligatorios.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO trips (user_id, title, subtitle, city, date_range, travelers) VALUES (?,?,?,?,?,?)');
        $stmt->execute([$usuario['id'], $title, $subtitle, $city, $date_range, $travelers]);
        $id = (int)$pdo->lastInsertId();
        setFlash('exito', 'Viaje creado correctamente.');
        header("Location: /itinerario.php?id=$id"); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo viaje · waydi</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>

<div class="topbar">
    <div class="topbar-logo">
        <a href="/index.php" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;">
            <span class="w">W</span> waydi
        </a>
    </div>
    <div class="topbar-right">
        <span class="topbar-user"><?= e($usuario['name']) ?></span>
        <a href="/index.php" class="btn-logout">← Mis viajes</a>
    </div>
</div>

<div class="form-card">
    <h1>Nuevo viaje</h1>

    <?php if ($error): ?>
        <div class="alerta alerta-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="campo-form">
            <label for="title">Título del viaje *</label>
            <input type="text" id="title" name="title" value="<?= e($_POST['title'] ?? '') ?>" placeholder="Ej: Escapada a París" required>
        </div>
        <div class="campo-form">
            <label for="subtitle">Subtítulo</label>
            <input type="text" id="subtitle" name="subtitle" value="<?= e($_POST['subtitle'] ?? '') ?>" placeholder="Ej: Una semana de arte y gastronomía">
        </div>
        <div class="fila-2">
            <div class="campo-form">
                <label for="city">Ciudad *</label>
                <input type="text" id="city" name="city" value="<?= e($_POST['city'] ?? '') ?>" placeholder="Ej: París" required>
            </div>
            <div class="campo-form">
                <label for="travelers">Viajeros</label>
                <input type="number" id="travelers" name="travelers" value="<?= e($_POST['travelers'] ?? 1) ?>" min="1" max="50">
            </div>
        </div>
        <div class="campo-form">
            <label for="date_range">Fechas</label>
            <input type="text" id="date_range" name="date_range" value="<?= e($_POST['date_range'] ?? '') ?>" placeholder="Ej: 12-18 Jun 2025">
        </div>
        <div style="display:flex;gap:12px;margin-top:8px;">
            <button type="submit" class="btn-nuevo">Crear viaje</button>
            <a href="/index.php" class="btn-logout" style="padding:11px 18px;">Cancelar</a>
        </div>
    </form>
</div>

<p class="pie">waydi · planifica suave, viaja ligero</p>
</body>
</html>
