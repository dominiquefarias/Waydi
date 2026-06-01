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
$totalActs     = array_sum(array_map(fn($d) => count($d['actividades']), $dias));
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
    <div class="hero-top">
        <div>
            <div class="hero-meta">
                <span><?= e($viaje['city']) ?></span>
                <?php if ($viaje['date_range']): ?>
                    <span class="sep">·</span>
                    <span><?= e($viaje['date_range']) ?></span>
                <?php endif; ?>
                <?php if ($viaje['travelers']): ?>
                    <span class="sep">·</span>
                    <span>👥 <?= e($viaje['travelers']) ?></span>
                <?php endif; ?>
            </div>
            <h1><?= e($viaje['title']) ?></h1>
            <?php if ($viaje['subtitle']): ?>
                <p class="hero-sub"><?= e($viaje['subtitle']) ?></p>
            <?php endif; ?>
        </div>
        <?php if ($dias): ?>
        <div class="hero-stats">
            <div class="stat-box">
                <div class="stat-box-num"><?= count($dias) ?></div>
                <div class="stat-box-lbl">días</div>
            </div>
            <div class="stat-box">
                <div class="stat-box-num"><?= $totalActs ?></div>
                <div class="stat-box-lbl">actividades</div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($dias): ?>
    <div class="tabs" id="tabs-dias">
        <?php foreach ($dias as $i => $dia): ?>
            <button class="tab <?= $i === 0 ? 'activo' : '' ?>"
                    onclick="cambiarDia(<?= $i ?>)"
                    data-dia="<?= $i ?>">
                <span class="tab-num"><?= $i + 1 ?></span>
                <div class="tab-info">
                    <div class="tab-dia"><?= e($dia['label']) ?></div>
                    <?php if ($dia['weekday']): ?>
                        <div class="tab-fecha"><?= e($dia['weekday']) ?></div>
                    <?php elseif ($dia['date']): ?>
                        <div class="tab-fecha"><?= e($dia['date']) ?></div>
                    <?php endif; ?>
                </div>
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
                <div class="tl-header">
                    <div>
                        <div class="tl-titulo"><?= e($dia['label']) ?></div>
                        <?php if ($dia['date']): ?>
                            <div class="tl-sub"><?= e($dia['date']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="tl-nav">
                        <button class="btn-nav" onclick="cambiarDia(<?= max(0, $i-1) ?>)" <?= $i === 0 ? 'disabled' : '' ?>>‹</button>
                        <button class="btn-nav" onclick="cambiarDia(<?= min(count($dias)-1, $i+1) ?>)" <?= $i === count($dias)-1 ? 'disabled' : '' ?>>›</button>
                    </div>
                </div>

                <?php if ($dia['actividades']): ?>
                <div class="tl-items">
                    <?php foreach ($dia['actividades'] as $j => $act): ?>
                    <?php $cat = slugCategoria($act['category'] ?? ''); ?>
                    <div class="tl-item" id="act-<?= $i ?>-<?= $j ?>"
                         onclick="seleccionarActividad(<?= $i ?>, <?= $j ?>)">
                        <div class="tl-tiempo">
                            <?php if ($act['time']): ?>
                                <span class="tl-hora"><?= e($act['time']) ?></span>
                            <?php endif; ?>
                            <?php if ($act['duration']): ?>
                                <span class="tl-dur"><?= e($act['duration']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="tl-dot-wrap">
                            <div class="tl-dot dot-<?= e($cat) ?>"></div>
                        </div>
                        <div class="tl-card-wrap">
                            <div class="tl-card">
                                <div class="tl-chips">
                                    <span class="act-chip <?= claseCategoria($cat) ?>">
                                        <?= iconoCategoria($cat) ?> <?= e($act['category'] ?: '') ?>
                                    </span>
                                    <?php if ($act['pin_x'] || $act['pin_y']): ?>
                                        <span class="en-mapa-badge">📍 en mapa</span>
                                    <?php endif; ?>
                                </div>
                                <div class="tl-body">
                                    <div class="tl-icon icon-<?= e($cat) ?>"><?= iconoCategoria($cat) ?></div>
                                    <div class="tl-info">
                                        <div class="tl-nombre"><?= e($act['name']) ?></div>
                                        <?php if ($act['place']): ?>
                                            <div class="tl-lugar">📍 <?= e($act['place']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="tl-num"><?= $j + 1 ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                    <p style="color:var(--faint);font-size:13.5px;text-align:center;padding:20px 0;">
                        Sin actividades este día
                    </p>
                <?php endif; ?>

                <?php if ($esPropietario): ?>
                <button class="btn-add-act">+ Añadir actividad</button>
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
                    <div class="mapa-rio"></div>
                    <div class="mapa-parque"></div>
                    <div class="mapa-calle" style="top:45%"></div>
                    <?php foreach ($dia['actividades'] as $j => $act): ?>
                        <?php if ($act['pin_x'] || $act['pin_y']): ?>
                        <div class="pin" id="pin-<?= $i ?>-<?= $j ?>"
                             style="left:<?= (float)$act['pin_x'] ?>%;top:<?= (float)$act['pin_y'] ?>%"
                             onclick="seleccionarActividad(<?= $i ?>, <?= $j ?>)"
                             title="<?= e($act['name']) ?>">
                            <div class="pin-num"><?= $j + 1 ?></div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <span class="ciudad-label">📍 <?= e($viaje['city']) ?></span>
                    <button class="ver-mapa-btn">↗ Ver en mapa</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Notas -->
        <?php foreach ($dias as $i => $dia): ?>
        <?php if ($dia['notas']): ?>
        <div class="notas-card" id="notas-<?= $i ?>" style="<?= $i > 0 ? 'display:none' : '' ?>">
            <div class="notas-header">
                <div class="notas-titulo">Notas del día</div>
                <span class="notas-count"><?= count($dia['notas']) ?></span>
            </div>
            <?php foreach ($dia['notas'] as $nota): ?>
            <div class="nota nota-tono-<?= e($nota['tone']) ?>">
                <div class="nota-icon-wrap"><?= $nota['icon'] ? e($nota['icon']) : '📝' ?></div>
                <div class="nota-body">
                    <div class="nota-titulo"><?= e($nota['title']) ?></div>
                    <div class="nota-texto"><?= e($nota['text']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
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
