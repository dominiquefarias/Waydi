<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/funciones.php';

$usuario = requireLogin();
$pdo     = getDB();
$tripId  = (int)($_GET['id'] ?? 0);

if (!$tripId) { header('Location: /index.php'); exit; }

// Verificar acceso (propietario o colaborador aceptado)
$stmt = $pdo->prepare('
    SELECT t.* FROM trips t
    LEFT JOIN trip_shares s ON s.trip_id = t.id AND s.user_id = ? AND s.status = "accepted"
    WHERE t.id = ? AND (t.user_id = ? OR s.id IS NOT NULL)
');
$stmt->execute([$usuario['id'], $tripId, $usuario['id']]);
$viaje = $stmt->fetch();

if (!$viaje) { header('Location: /index.php'); exit; }

// Cargar días con actividades y notas
$diasStmt = $pdo->prepare('SELECT * FROM days WHERE trip_id = ? ORDER BY position');
$diasStmt->execute([$tripId]);
$dias = $diasStmt->fetchAll();

foreach ($dias as &$dia) {
    $aStmt = $pdo->prepare('SELECT * FROM activities WHERE day_id = ? ORDER BY position');
    $aStmt->execute([$dia['id']]);
    $dia['actividades'] = $aStmt->fetchAll();

    $nStmt = $pdo->prepare('SELECT * FROM notes WHERE day_id = ? ORDER BY position');
    $nStmt->execute([$dia['id']]);
    $dia['notas'] = $nStmt->fetchAll();
}
unset($dia);

$esPropietario = ($viaje['user_id'] === $usuario['id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($viaje['title']) ?> · waydi</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>

<!-- Topbar -->
<div class="topbar">
    <div class="topbar-logo">
        <a href="/index.php" style="display:flex;align-items:center;gap:10px;color:inherit;">
            <span class="w">W</span> waydi
        </a>
    </div>
    <div class="topbar-right">
        <span class="topbar-user"><?= e($usuario['name']) ?></span>
        <?php if ($usuario['is_admin']): ?>
            <a href="/admin/" class="btn-logout">Admin</a>
        <?php endif; ?>
        <a href="/index.php" class="btn-logout">Mis viajes</a>
        <a href="/logout.php" class="btn-logout">Salir</a>
    </div>
</div>

<!-- Hero -->
<div class="hero">
    <div class="hero-meta">
        <span><?= e($viaje['city']) ?></span>
        <span><?= e($viaje['date_range']) ?></span>
        <span>👥 <?= e($viaje['travelers']) ?></span>
    </div>
    <h1><?= e($viaje['title']) ?></h1>
    <?php if ($viaje['subtitle']): ?>
        <p class="hero-sub"><?= e($viaje['subtitle']) ?></p>
    <?php endif; ?>

    <?php if ($dias): ?>
    <div class="tabs" id="tabs-dias">
        <?php foreach ($dias as $i => $dia): ?>
            <button class="tab <?= $i === 0 ? 'activo' : '' ?>"
                    onclick="cambiarDia(<?= $i ?>)"
                    data-dia="<?= $i ?>">
                <?= e($dia['label']) ?>
                <?php if ($dia['weekday']): ?>
                    <span style="font-weight:400;opacity:.7"> · <?= e($dia['weekday']) ?></span>
                <?php endif; ?>
            </button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php if (!$dias): ?>
    <div style="background:var(--white);border-radius:var(--radius);padding:40px;text-align:center;box-shadow:var(--shadow);">
        <p style="color:var(--muted);font-size:15px;">Este viaje aún no tiene días ni actividades.</p>
    </div>
<?php else: ?>

<div class="contenido">

    <!-- Timeline -->
    <div>
        <?php foreach ($dias as $i => $dia): ?>
        <div class="dia-seccion" id="dia-<?= $i ?>" style="<?= $i > 0 ? 'display:none' : '' ?>">
            <div class="timeline-card">
                <div class="timeline-header">
                    <span class="timeline-titulo">
                        <?= e($dia['label']) ?>
                        <?php if ($dia['date']): ?>
                            <span style="font-weight:500;color:var(--faint);font-size:13px"> · <?= e($dia['date']) ?></span>
                        <?php endif; ?>
                    </span>
                    <div class="timeline-nav">
                        <button class="btn-nav" onclick="cambiarDia(<?= max(0, $i-1) ?>)" <?= $i === 0 ? 'disabled style="opacity:.35"' : '' ?>>‹</button>
                        <button class="btn-nav" onclick="cambiarDia(<?= min(count($dias)-1, $i+1) ?>)" <?= $i === count($dias)-1 ? 'disabled style="opacity:.35"' : '' ?>>›</button>
                    </div>
                </div>

                <?php if ($dia['actividades']): ?>
                    <?php foreach ($dia['actividades'] as $j => $act): ?>
                    <div class="actividad" id="act-<?= $i ?>-<?= $j ?>"
                         onclick="seleccionarActividad(<?= $i ?>, <?= $j ?>)">
                        <div class="act-num"><?= $j + 1 ?></div>
                        <div class="act-info">
                            <div class="act-tiempo">
                                <?= e($act['time']) ?>
                                <?php if ($act['duration']): ?>
                                    · <?= e($act['duration']) ?>
                                <?php endif; ?>
                            </div>
                            <div class="act-nombre"><?= e($act['name']) ?></div>
                            <?php if ($act['place']): ?>
                                <div class="act-lugar">📍 <?= e($act['place']) ?></div>
                            <?php endif; ?>
                            <?php if ($act['category']): ?>
                                <span class="act-chip <?= claseCategoria($act['category']) ?>">
                                    <?= iconoCategoria($act['category']) ?> <?= e($act['category']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color:var(--faint);font-size:13.5px;text-align:center;padding:20px 0;">
                        Sin actividades este día
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Mapa + Notas -->
    <div class="columna-derecha">

        <!-- Mapa -->
        <div class="mapa-card">
            <div class="mapa-titulo">Mapa</div>
            <?php foreach ($dias as $i => $dia): ?>
            <div id="mapa-<?= $i ?>" style="<?= $i > 0 ? 'display:none' : '' ?>">
                <div class="mapa">
                    <?php foreach ($dia['actividades'] as $j => $act): ?>
                        <?php if ($act['pin_x'] || $act['pin_y']): ?>
                        <div class="pin" id="pin-<?= $i ?>-<?= $j ?>"
                             style="left:<?= (float)$act['pin_x'] ?>%;top:<?= (float)$act['pin_y'] ?>%"
                             onclick="seleccionarActividad(<?= $i ?>, <?= $j ?>)"
                             title="<?= e($act['name']) ?>">
                            <?= $j + 1 ?>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <span class="ciudad-label"><?= e($viaje['city']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Notas -->
        <?php foreach ($dias as $i => $dia): ?>
        <?php if ($dia['notas']): ?>
        <div class="notas-card" id="notas-<?= $i ?>" style="<?= $i > 0 ? 'display:none' : '' ?>">
            <div class="notas-titulo">Notas del día</div>
            <div class="notas-grid">
                <?php foreach ($dia['notas'] as $nota): ?>
                <div class="nota nota-tono-<?= e($nota['tone']) ?>">
                    <?php if ($nota['icon']): ?>
                        <div class="nota-icono"><?= e($nota['icon']) ?></div>
                    <?php endif; ?>
                    <div class="nota-titulo"><?= e($nota['title']) ?></div>
                    <div class="nota-texto"><?= e($nota['text']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>

    </div>
</div>

<?php endif; ?>

<p class="pie">waydi · planifica suave, viaja ligero</p>

<script src="/js/itinerario.js"></script>
<script>
    const TOTAL_DIAS = <?= count($dias) ?>;
</script>
</body>
</html>
