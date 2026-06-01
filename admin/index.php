<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/funciones.php';

$usuario = requireAdmin();
$pdo     = getDB();

// ── Procesar acciones POST ───────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'eliminar_usuario') {
        $id = (int)$_POST['id'];
        if ($id !== $usuario['id']) { // no puede eliminarse a sí mismo
            $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
            setFlash('exito', 'Usuario eliminado.');
        }
    } elseif ($accion === 'cambiar_admin') {
        $id  = (int)$_POST['id'];
        $val = (int)$_POST['valor'];
        if ($id !== $usuario['id']) {
            $pdo->prepare('UPDATE users SET is_admin = ? WHERE id = ?')->execute([$val, $id]);
            setFlash('exito', 'Rol actualizado.');
        }
    } elseif ($accion === 'eliminar_viaje') {
        $id = (int)$_POST['id'];
        $pdo->prepare('DELETE FROM trips WHERE id = ?')->execute([$id]);
        setFlash('exito', 'Viaje eliminado.');
    }

    header('Location: /admin/?tab=' . ($_POST['tab'] ?? 'dashboard'));
    exit;
}

$tab = $_GET['tab'] ?? 'dashboard';

// ── Estadísticas ─────────────────────────────────────────
$stats = $pdo->query('
    SELECT
        (SELECT COUNT(*) FROM users)       AS total_usuarios,
        (SELECT COUNT(*) FROM trips)       AS total_viajes,
        (SELECT COUNT(*) FROM activities)  AS total_actividades,
        (SELECT COUNT(*) FROM trip_shares) AS total_compartidos,
        (SELECT COUNT(*) FROM users      WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) AS nuevos_semana,
        (SELECT COUNT(*) FROM trips      WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) AS viajes_semana
')->fetch();

// ── Usuarios ─────────────────────────────────────────────
$buscarUsuario = trim($_GET['q_usuario'] ?? '');
$sqlU = 'SELECT u.*, (SELECT COUNT(*) FROM trips t WHERE t.user_id = u.id) AS total_viajes
         FROM users u';
if ($buscarUsuario) {
    $sqlU .= ' WHERE u.name LIKE ? OR u.email LIKE ?';
    $stmtU = $pdo->prepare($sqlU . ' ORDER BY u.created_at DESC');
    $like  = "%$buscarUsuario%";
    $stmtU->execute([$like, $like]);
} else {
    $stmtU = $pdo->query($sqlU . ' ORDER BY u.created_at DESC');
}
$usuarios = $stmtU->fetchAll();

// ── Viajes ────────────────────────────────────────────────
$buscarViaje = trim($_GET['q_viaje'] ?? '');
$sqlV = 'SELECT t.*, u.name AS owner_name, u.email AS owner_email,
                (SELECT COUNT(*) FROM days d WHERE d.trip_id = t.id)       AS total_dias,
                (SELECT COUNT(*) FROM activities a JOIN days d ON d.id = a.day_id WHERE d.trip_id = t.id) AS total_actividades
         FROM trips t JOIN users u ON u.id = t.user_id';
if ($buscarViaje) {
    $sqlV .= ' WHERE t.title LIKE ? OR t.city LIKE ? OR u.name LIKE ?';
    $stmtV = $pdo->prepare($sqlV . ' ORDER BY t.created_at DESC');
    $like  = "%$buscarViaje%";
    $stmtV->execute([$like, $like, $like]);
} else {
    $stmtV = $pdo->query($sqlV . ' ORDER BY t.created_at DESC');
}
$viajes = $stmtV->fetchAll();

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin · waydi</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body style="padding:20px;">

<div class="admin-layout">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <span class="w">W</span> waydi
        </div>
        <nav class="sidebar-nav">
            <button class="nav-item <?= $tab === 'dashboard' ? 'activo' : '' ?>"
                    onclick="cambiarTab('dashboard')">
                <span class="nav-icono">📊</span> Dashboard
            </button>
            <button class="nav-item <?= $tab === 'usuarios' ? 'activo' : '' ?>"
                    onclick="cambiarTab('usuarios')">
                <span class="nav-icono">👥</span> Usuarios
                <span style="margin-left:auto;font-size:11px;background:var(--lavender);padding:1px 7px;border-radius:99px;">
                    <?= count($usuarios) ?>
                </span>
            </button>
            <button class="nav-item <?= $tab === 'viajes' ? 'activo' : '' ?>"
                    onclick="cambiarTab('viajes')">
                <span class="nav-icono">✈️</span> Viajes
                <span style="margin-left:auto;font-size:11px;background:var(--lavender);padding:1px 7px;border-radius:99px;">
                    <?= count($viajes) ?>
                </span>
            </button>
        </nav>
        <div class="sidebar-footer">
            <div class="sidebar-user"><?= e($usuario['name']) ?></div>
            <a href="/index.php" class="btn-logout" style="display:block;text-align:center;">← App</a>
        </div>
    </aside>

    <!-- Contenido principal -->
    <main class="admin-main">

        <?php if ($flash): ?>
            <div class="alerta alerta-<?= e($flash['tipo']) ?>"><?= e($flash['msg']) ?></div>
        <?php endif; ?>

        <!-- ── DASHBOARD ───────────────────────────── -->
        <div class="admin-panel <?= $tab === 'dashboard' ? 'activo' : '' ?>" id="panel-dashboard">

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Usuarios</div>
                    <div class="stat-num"><?= $stats['total_usuarios'] ?></div>
                    <div class="stat-sub">+<?= $stats['nuevos_semana'] ?> esta semana</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Viajes</div>
                    <div class="stat-num"><?= $stats['total_viajes'] ?></div>
                    <div class="stat-sub">+<?= $stats['viajes_semana'] ?> esta semana</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Actividades</div>
                    <div class="stat-num"><?= $stats['total_actividades'] ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Viajes compartidos</div>
                    <div class="stat-num"><?= $stats['total_compartidos'] ?></div>
                </div>
            </div>

            <!-- Top categorías -->
            <?php
            $cats = $pdo->query('SELECT category, COUNT(*) AS total FROM activities GROUP BY category ORDER BY total DESC LIMIT 8')->fetchAll();
            $maxCat = $cats ? max(array_column($cats, 'total')) : 1;
            ?>
            <?php if ($cats): ?>
            <div class="tabla-card">
                <div class="tabla-header"><h2>Categorías más usadas</h2></div>
                <?php foreach ($cats as $c): ?>
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                    <span style="min-width:110px;font-size:13px;font-weight:600;"><?= e($c['category']) ?></span>
                    <div style="flex:1;background:var(--lavender);border-radius:99px;height:8px;">
                        <div style="width:<?= round($c['total']/$maxCat*100) ?>%;background:var(--lila-deep);height:8px;border-radius:99px;"></div>
                    </div>
                    <span style="font-size:13px;font-weight:700;min-width:28px;text-align:right;"><?= $c['total'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- ── USUARIOS ────────────────────────────── -->
        <div class="admin-panel <?= $tab === 'usuarios' ? 'activo' : '' ?>" id="panel-usuarios">
            <div class="tabla-card">
                <div class="tabla-header">
                    <h2>Usuarios</h2>
                    <form method="GET" class="buscar">
                        <input type="hidden" name="tab" value="usuarios">
                        <input type="text" name="q_usuario" value="<?= e($buscarUsuario) ?>" placeholder="Buscar por nombre o email…">
                        <button type="submit">Buscar</button>
                    </form>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Viajes</th>
                            <th>Rol</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td>
                                <span class="avatar"><?= mb_strtoupper(mb_substr($u['name'], 0, 1)) ?></span>
                                <?= e($u['name']) ?>
                            </td>
                            <td style="color:var(--muted)"><?= e($u['email']) ?></td>
                            <td><?= $u['total_viajes'] ?></td>
                            <td>
                                <span class="badge <?= $u['is_admin'] ? 'badge-admin' : 'badge-user' ?>">
                                    <?= $u['is_admin'] ? 'Admin' : 'Usuario' ?>
                                </span>
                            </td>
                            <td style="color:var(--faint)"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                            <td>
                                <div class="acciones">
                                    <?php if ($u['id'] !== $usuario['id']): ?>
                                        <?php if ($u['is_admin']): ?>
                                            <form method="POST" style="display:inline">
                                                <input type="hidden" name="accion" value="cambiar_admin">
                                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                <input type="hidden" name="valor" value="0">
                                                <input type="hidden" name="tab" value="usuarios">
                                                <button type="submit" class="btn-sm btn-warning">Quitar admin</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" style="display:inline">
                                                <input type="hidden" name="accion" value="cambiar_admin">
                                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                <input type="hidden" name="valor" value="1">
                                                <input type="hidden" name="tab" value="usuarios">
                                                <button type="submit" class="btn-sm btn-success">Hacer admin</button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" style="display:inline"
                                              onsubmit="return confirm('¿Eliminar a <?= e(addslashes($u['name'])) ?>?')">
                                            <input type="hidden" name="accion" value="eliminar_usuario">
                                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                            <input type="hidden" name="tab" value="usuarios">
                                            <button type="submit" class="btn-sm btn-danger">Eliminar</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="font-size:12px;color:var(--faint)">Tú</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── VIAJES ──────────────────────────────── -->
        <div class="admin-panel <?= $tab === 'viajes' ? 'activo' : '' ?>" id="panel-viajes">
            <div class="tabla-card">
                <div class="tabla-header">
                    <h2>Viajes</h2>
                    <form method="GET" class="buscar">
                        <input type="hidden" name="tab" value="viajes">
                        <input type="text" name="q_viaje" value="<?= e($buscarViaje) ?>" placeholder="Buscar por título, ciudad…">
                        <button type="submit">Buscar</button>
                    </form>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Viaje</th>
                            <th>Ciudad</th>
                            <th>Propietario</th>
                            <th>Días</th>
                            <th>Actividades</th>
                            <th>Creado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($viajes as $v): ?>
                        <tr>
                            <td style="font-weight:600"><?= e($v['title']) ?></td>
                            <td style="color:var(--muted)"><?= e($v['city']) ?></td>
                            <td>
                                <div style="font-size:13px"><?= e($v['owner_name']) ?></div>
                                <div style="font-size:11.5px;color:var(--faint)"><?= e($v['owner_email']) ?></div>
                            </td>
                            <td><?= $v['total_dias'] ?></td>
                            <td><?= $v['total_actividades'] ?></td>
                            <td style="color:var(--faint)"><?= date('d/m/Y', strtotime($v['created_at'])) ?></td>
                            <td>
                                <div class="acciones">
                                    <a href="/itinerario.php?id=<?= $v['id'] ?>" class="btn-sm btn-info">Ver</a>
                                    <form method="POST" style="display:inline"
                                          onsubmit="return confirm('¿Eliminar «<?= e(addslashes($v['title'])) ?>»?')">
                                        <input type="hidden" name="accion" value="eliminar_viaje">
                                        <input type="hidden" name="id" value="<?= $v['id'] ?>">
                                        <input type="hidden" name="tab" value="viajes">
                                        <button type="submit" class="btn-sm btn-danger">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<script src="/js/admin.js"></script>
<script>
    // Restaurar tab activo al volver de un POST
    const tabActual = new URLSearchParams(location.search).get('tab') || '<?= e($tab) ?>';
    cambiarTab(tabActual, false);
</script>
</body>
</html>
