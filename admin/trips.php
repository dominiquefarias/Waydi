<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
requireAuth();

$db = getDB();

// Demo data
$trips = [
  ['id'=>1,'title'=>'Aventura en París','city'=>'París, Francia','travelers'=>2,'created_at'=>'2025-06-01','days'=>4,'activities'=>20],
  ['id'=>2,'title'=>'Tokyo Express','city'=>'Tokio, Japón','travelers'=>1,'created_at'=>'2025-05-28','days'=>7,'activities'=>35],
  ['id'=>3,'title'=>'Roma Clásica','city'=>'Roma, Italia','travelers'=>4,'created_at'=>'2025-05-20','days'=>5,'activities'=>22],
  ['id'=>4,'title'=>'Safari Kenya','city'=>'Nairobi, Kenia','travelers'=>2,'created_at'=>'2025-05-15','days'=>6,'activities'=>18],
  ['id'=>5,'title'=>'NY City Vibes','city'=>'Nueva York, USA','travelers'=>3,'created_at'=>'2025-05-10','days'=>3,'activities'=>14],
];

if ($db) {
    try {
        $rows = $db->query('
            SELECT t.id, t.title, t.city, t.travelers, t.created_at,
                   COUNT(DISTINCT d.id) AS days,
                   COUNT(DISTINCT a.id) AS activities
            FROM trips t
            LEFT JOIN days d ON d.trip_id = t.id
            LEFT JOIN activities a ON a.day_id = d.id
            GROUP BY t.id ORDER BY t.created_at DESC
        ')->fetchAll();
        if ($rows) $trips = $rows;
    } catch (PDOException) {}
}

$msg = '';
if (isset($_GET['deleted'])) $msg = 'Viaje eliminado.';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Viajes · waydi Admin</title>
  <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<div class="admin-wrap">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>

  <main class="main">
    <div class="topbar">
      <div>
        <div class="page-title">Viajes</div>
        <div class="page-sub">Gestiona todos los itinerarios · <?= count($trips) ?> viajes en total</div>
      </div>
      <button class="btn btn-primary" onclick="alert('Formulario de nuevo viaje (en desarrollo)')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nuevo viaje
      </button>
    </div>

    <?php if ($msg): ?>
      <div class="alert alert-error" style="background:#DDF2E8;color:#4C9C77;"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Título</th>
              <th>Ciudad</th>
              <th>Viajeros</th>
              <th>Días</th>
              <th>Actividades</th>
              <th>Creado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($trips as $t): ?>
            <tr>
              <td class="td-muted"><?= $t['id'] ?></td>
              <td class="td-bold"><?= htmlspecialchars($t['title']) ?></td>
              <td>
                <div style="display:flex;align-items:center;gap:6px;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="var(--lila-deep)" stroke-width="2" style="width:13px;height:13px;flex-shrink:0"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                  <span class="td-muted"><?= htmlspecialchars($t['city'] ?? '—') ?></span>
                </div>
              </td>
              <td class="td-muted"><?= $t['travelers'] ?? 1 ?></td>
              <td><span class="badge badge-lila"><?= $t['days'] ?? 0 ?> días</span></td>
              <td><span class="badge badge-green"><?= $t['activities'] ?? 0 ?></span></td>
              <td class="td-muted"><?= substr($t['created_at'] ?? '', 0, 10) ?></td>
              <td>
                <div style="display:flex;gap:6px;">
                  <button class="btn btn-ghost btn-sm" onclick="alert('Editar viaje #<?= $t['id'] ?>')">Editar</button>
                  <button class="btn btn-danger btn-sm" onclick="if(confirm('¿Eliminar este viaje?')) window.location='trips.php?delete=<?= $t['id'] ?>'">Eliminar</button>
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
</body>
</html>
