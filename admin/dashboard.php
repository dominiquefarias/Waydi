<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
requireAuth();

if (isset($_GET['logout'])) doLogout();

$db = getDB();

// Demo stats (fallback if no DB)
$stats = ['trips' => 12, 'users' => 48, 'activities' => 157, 'sessions' => 3];
$recentTrips = [
  ['id'=>1,'title'=>'Aventura en París','city'=>'París, Francia','created_at'=>'2025-06-01','status'=>'activo'],
  ['id'=>2,'title'=>'Tokyo Express','city'=>'Tokio, Japón','created_at'=>'2025-05-28','status'=>'activo'],
  ['id'=>3,'title'=>'Roma Clásica','city'=>'Roma, Italia','created_at'=>'2025-05-20','status'=>'borrador'],
  ['id'=>4,'title'=>'Safari Kenya','city'=>'Nairobi, Kenia','created_at'=>'2025-05-15','status'=>'activo'],
  ['id'=>5,'title'=>'NY City Vibes','city'=>'Nueva York, USA','created_at'=>'2025-05-10','status'=>'archivado'],
];

if ($db) {
    try {
        $stats['trips']      = $db->query('SELECT COUNT(*) FROM trips')->fetchColumn();
        $stats['users']      = $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $stats['activities'] = $db->query('SELECT COUNT(*) FROM activities')->fetchColumn();
        $recentTrips = $db->query('SELECT t.id, t.title, t.city, t.created_at, "activo" AS status FROM trips t ORDER BY t.created_at DESC LIMIT 5')->fetchAll();
    } catch (PDOException) {}
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard · waydi Admin</title>
  <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<div class="admin-wrap">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>

  <main class="main">
    <div class="topbar">
      <div>
        <div class="page-title">Dashboard</div>
        <div class="page-sub">Bienvenido, <?= htmlspecialchars($_SESSION['admin_user'] ?? 'admin') ?>. Aquí está tu resumen.</div>
      </div>
      <a href="trips.php" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nuevo viaje
      </a>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon" style="background:#ECE3FF;">
          <svg viewBox="0 0 24 24" fill="none" stroke="#6A48C0" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
        <div class="stat-label">Total viajes</div>
        <div class="stat-value"><?= $stats['trips'] ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#FCE0EC;">
          <svg viewBox="0 0 24 24" fill="none" stroke="#C0567E" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="stat-label">Usuarios</div>
        <div class="stat-value"><?= $stats['users'] ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#DDF2E8;">
          <svg viewBox="0 0 24 24" fill="none" stroke="#4C9C77" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>
        <div class="stat-label">Actividades</div>
        <div class="stat-value"><?= $stats['activities'] ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#E3ECFF;">
          <svg viewBox="0 0 24 24" fill="none" stroke="#5772BE" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-label">Sesiones activas</div>
        <div class="stat-value"><?= $stats['sessions'] ?></div>
      </div>
    </div>

    <div class="grid-2">
      <!-- Recent trips -->
      <div class="card">
        <div class="section-header">
          <div class="section-title">Viajes recientes</div>
          <a href="trips.php" class="btn btn-ghost btn-sm">Ver todos</a>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Viaje</th>
                <th>Ciudad</th>
                <th>Estado</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentTrips as $t): ?>
              <tr>
                <td class="td-bold"><?= htmlspecialchars($t['title']) ?></td>
                <td class="td-muted"><?= htmlspecialchars($t['city'] ?? '—') ?></td>
                <td>
                  <?php
                    $status = $t['status'] ?? 'activo';
                    $badgeClass = match($status) {
                      'activo'    => 'badge-green',
                      'borrador'  => 'badge-orange',
                      'archivado' => 'badge-lila',
                      default     => 'badge-lila',
                    };
                  ?>
                  <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                </td>
                <td><a href="trips.php?edit=<?= $t['id'] ?>" class="btn btn-ghost btn-sm">Editar</a></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Quick actions -->
      <div class="card">
        <div class="section-header">
          <div class="section-title">Acciones rápidas</div>
        </div>
        <div class="actions-grid">
          <a href="trips.php?new=1" class="action-card">
            <div class="action-icon" style="background:#ECE3FF;">
              <svg viewBox="0 0 24 24" fill="none" stroke="#6A48C0" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </div>
            <div class="action-label">Nuevo viaje</div>
            <div class="action-desc">Crear itinerario</div>
          </a>
          <a href="users.php?new=1" class="action-card">
            <div class="action-icon" style="background:#FCE0EC;">
              <svg viewBox="0 0 24 24" fill="none" stroke="#C0567E" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
            </div>
            <div class="action-label">Nuevo usuario</div>
            <div class="action-desc">Agregar cuenta</div>
          </a>
          <a href="trips.php" class="action-card">
            <div class="action-icon" style="background:#DDF2E8;">
              <svg viewBox="0 0 24 24" fill="none" stroke="#4C9C77" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <div class="action-label">Ver reportes</div>
            <div class="action-desc">Estadísticas</div>
          </a>
          <a href="?logout=1" class="action-card" onclick="return confirm('¿Cerrar sesión?')">
            <div class="action-icon" style="background:#E9E5F5;">
              <svg viewBox="0 0 24 24" fill="none" stroke="#766AA4" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </div>
            <div class="action-label">Cerrar sesión</div>
            <div class="action-desc">Salir del panel</div>
          </a>
        </div>
      </div>
    </div>
  </main>
</div>
</body>
</html>
