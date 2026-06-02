<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/layout.php';
requireLogin();

$trips = [];
$message = '';

try {
    $db = getDB();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete'])) {
            $db->prepare("DELETE FROM trips WHERE id = ?")->execute([$_POST['delete']]);
            $message = 'Viaje eliminado correctamente.';
        } elseif (isset($_POST['save'])) {
            if (!empty($_POST['id'])) {
                $db->prepare("UPDATE trips SET title=?, city=?, start_date=?, end_date=? WHERE id=?")
                   ->execute([$_POST['title'], $_POST['city'], $_POST['start_date'], $_POST['end_date'], $_POST['id']]);
                $message = 'Viaje actualizado correctamente.';
            } else {
                $db->prepare("INSERT INTO trips (title, city, start_date, end_date, user_id, created_at) VALUES (?,?,?,?,1,NOW())")
                   ->execute([$_POST['title'], $_POST['city'], $_POST['start_date'], $_POST['end_date']]);
                $message = 'Viaje creado correctamente.';
            }
        }
    }

    $trips = $db->query(
        "SELECT t.*, u.name as owner, COUNT(a.id) as activity_count
         FROM trips t LEFT JOIN users u ON u.id = t.user_id
         LEFT JOIN activities a ON a.trip_id = t.id
         GROUP BY t.id ORDER BY t.created_at DESC"
    )->fetchAll();
} catch (Exception $e) {
    $trips = [
        ['id'=>1,'title'=>'Aventura en París','city'=>'París, Francia','start_date'=>'2026-06-12','end_date'=>'2026-06-15','owner'=>'Ana M.','activity_count'=>20],
        ['id'=>2,'title'=>'Tokio Moderno','city'=>'Tokio, Japón','start_date'=>'2026-08-01','end_date'=>'2026-08-10','owner'=>'Carlos R.','activity_count'=>32],
        ['id'=>3,'title'=>'Nueva York Express','city'=>'Nueva York, EEUU','start_date'=>'2026-09-15','end_date'=>'2026-09-19','owner'=>'María L.','activity_count'=>14],
    ];
}
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
<div class="admin-layout">
  <?php renderSidebar('trips.php'); ?>
  <div class="main-content">
    <header class="topbar">
      <div>
        <div class="topbar-title">Viajes</div>
        <div class="topbar-sub">Gestiona todos los itinerarios</div>
      </div>
      <div class="topbar-right">
        <a href="#form-crear" class="btn btn-primary btn-sm">+ Nuevo viaje</a>
        <div class="avatar"><?= strtoupper(substr($_SESSION['admin_user'] ?? 'A', 0, 1)) ?></div>
      </div>
    </header>

    <div class="page-body">
      <?php if ($message): ?>
        <div class="alert" style="background:#DDF2E8;color:#4C9C77;margin-bottom:20px;">✓ <?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <!-- Table -->
      <div class="card mb-6">
        <h2 class="font-extrabold mb-4" style="font-size:17px;">Todos los viajes</h2>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Título</th>
                <th>Ciudad</th>
                <th>Fechas</th>
                <th>Usuario</th>
                <th>Paradas</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($trips as $trip): ?>
              <tr>
                <td class="text-muted text-sm"><?= $trip['id'] ?></td>
                <td class="font-bold"><?= htmlspecialchars($trip['title']) ?></td>
                <td><?= htmlspecialchars($trip['city']) ?></td>
                <td class="text-sm text-muted">
                  <?= date('d M', strtotime($trip['start_date'])) ?> – <?= date('d M Y', strtotime($trip['end_date'])) ?>
                </td>
                <td><?= htmlspecialchars($trip['owner'] ?? '—') ?></td>
                <td><span class="badge badge-lila"><?= $trip['activity_count'] ?></span></td>
                <td class="flex gap-2">
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="delete" value="<?= $trip['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este viaje?')">Eliminar</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Create form -->
      <div class="card" id="form-crear">
        <h2 class="font-extrabold mb-4" style="font-size:17px;">Crear nuevo viaje</h2>
        <form method="POST" action="">
          <input type="hidden" name="save" value="1">
          <div class="grid-2">
            <div class="form-group">
              <label>Título del viaje</label>
              <input type="text" name="title" placeholder="Aventura en París" required>
            </div>
            <div class="form-group">
              <label>Ciudad / Destino</label>
              <input type="text" name="city" placeholder="París, Francia" required>
            </div>
            <div class="form-group">
              <label>Fecha de inicio</label>
              <input type="text" name="start_date" placeholder="2026-06-12" required>
            </div>
            <div class="form-group">
              <label>Fecha de fin</label>
              <input type="text" name="end_date" placeholder="2026-06-15" required>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Crear viaje</button>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>
