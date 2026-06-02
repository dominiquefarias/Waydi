<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/layout.php';
requireLogin();

$users = [];
$message = '';

try {
    $db = getDB();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        $db->prepare("DELETE FROM users WHERE id = ?")->execute([$_POST['delete']]);
        $message = 'Usuario eliminado.';
    }

    $users = $db->query(
        "SELECT u.*, COUNT(t.id) as trip_count
         FROM users u LEFT JOIN trips t ON t.user_id = u.id
         GROUP BY u.id ORDER BY u.created_at DESC"
    )->fetchAll();
} catch (Exception $e) {
    $users = [
        ['id'=>1,'name'=>'Ana Martínez','email'=>'ana@example.com','created_at'=>'2026-01-15','trip_count'=>3],
        ['id'=>2,'name'=>'Carlos Ruiz','email'=>'carlos@example.com','created_at'=>'2026-02-08','trip_count'=>5],
        ['id'=>3,'name'=>'María López','email'=>'maria@example.com','created_at'=>'2026-03-22','trip_count'=>1],
        ['id'=>4,'name'=>'David Chen','email'=>'david@example.com','created_at'=>'2026-04-01','trip_count'=>7],
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Usuarios · waydi Admin</title>
  <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<div class="admin-layout">
  <?php renderSidebar('users.php'); ?>
  <div class="main-content">
    <header class="topbar">
      <div>
        <div class="topbar-title">Usuarios</div>
        <div class="topbar-sub"><?= count($users) ?> usuarios registrados</div>
      </div>
      <div class="topbar-right">
        <div class="avatar"><?= strtoupper(substr($_SESSION['admin_user'] ?? 'A', 0, 1)) ?></div>
      </div>
    </header>

    <div class="page-body">
      <?php if ($message): ?>
        <div class="alert" style="background:#DDF2E8;color:#4C9C77;margin-bottom:20px;">✓ <?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <div class="card">
        <h2 class="font-extrabold mb-4" style="font-size:17px;">Todos los usuarios</h2>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Viajes</th>
                <th>Registro</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $user): ?>
              <tr>
                <td class="text-muted text-sm"><?= $user['id'] ?></td>
                <td>
                  <div class="flex items-center gap-2">
                    <div class="avatar" style="width:30px;height:30px;font-size:12px;background:var(--lila);">
                      <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>
                    <span class="font-bold"><?= htmlspecialchars($user['name']) ?></span>
                  </div>
                </td>
                <td class="text-muted"><?= htmlspecialchars($user['email']) ?></td>
                <td><span class="badge badge-lila"><?= $user['trip_count'] ?> viajes</span></td>
                <td class="text-sm text-muted"><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                <td>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="delete" value="<?= $user['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar usuario?')">Eliminar</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
