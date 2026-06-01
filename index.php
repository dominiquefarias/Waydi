<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/funciones.php';

$usuario = requireLogin();
$pdo     = getDB();
$flash   = getFlash();

// Viajes propios + viajes compartidos aceptados
$stmt = $pdo->prepare('
    SELECT t.*, u.name AS propietario
    FROM trips t
    JOIN users u ON u.id = t.user_id
    WHERE t.user_id = ?
    UNION
    SELECT t.*, u.name AS propietario
    FROM trips t
    JOIN users u ON u.id = t.user_id
    JOIN trip_shares s ON s.trip_id = t.id
    WHERE s.user_id = ? AND s.status = "accepted"
    ORDER BY created_at DESC
');
$stmt->execute([$usuario['id'], $usuario['id']]);
$viajes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis viajes · waydi</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>

<div class="topbar">
    <div class="topbar-logo">
        <span class="w">W</span> waydi
    </div>
    <div class="topbar-right">
        <span class="topbar-user"><?= e($usuario['name']) ?></span>
        <?php if ($usuario['is_admin']): ?>
            <a href="/admin/" class="btn-logout">Admin</a>
        <?php endif; ?>
        <a href="/logout.php" class="btn-logout">Cerrar sesión</a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alerta alerta-<?= e($flash['tipo']) ?>"><?= e($flash['msg']) ?></div>
<?php endif; ?>

<p class="page-titulo">Mis viajes</p>

<?php if ($viajes): ?>
    <div class="viajes-grid">
        <?php foreach ($viajes as $v): ?>
            <a href="/itinerario.php?id=<?= $v['id'] ?>" class="viaje-card">
                <div class="viaje-ciudad"><?= e($v['city']) ?></div>
                <div class="viaje-titulo"><?= e($v['title']) ?></div>
                <div class="viaje-sub"><?= e($v['subtitle']) ?></div>
                <div class="viaje-meta">
                    <span>📅 <?= e($v['date_range']) ?></span>
                    <span>👥 <?= e($v['travelers']) ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p style="color:var(--muted);margin-bottom:24px;">Aún no tienes viajes. ¡Crea el primero!</p>
<?php endif; ?>

<a href="/nuevo-viaje.php" class="btn-nuevo">+ Nuevo viaje</a>

<p class="pie">waydi · planifica suave, viaja ligero</p>
</body>
</html>
