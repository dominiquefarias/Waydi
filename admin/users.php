<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
requireAuth();

$db = getDB();

$users = [
  ['id'=>1,'name'=>'Ana García','email'=>'ana@example.com','is_admin'=>0,'trips'=>3,'created_at'=>'2025-05-01'],
  ['id'=>2,'name'=>'Miguel López','email'=>'miguel@example.com','is_admin'=>0,'trips'=>2,'created_at'=>'2025-05-05'],
  ['id'=>3,'name'=>'Sofia Martín','email'=>'sofia@example.com','is_admin'=>1,'trips'=>5,'created_at'=>'2025-04-20'],
  ['id'=>4,'name'=>'Carlos Ruiz','email'=>'carlos@example.com','is_admin'=>0,'trips'=>1,'created_at'=>'2025-05-15'],
  ['id'=>5,'name'=>'Laura Díaz','email'=>'laura@example.com','is_admin'=>0,'trips'=>4,'created_at'=>'2025-04-28'],
];

if ($db) {
    try {
        $rows = $db->query('
            SELECT u.id, u.name, u.email, u.is_admin, u.created_at,
                   COUNT(t.id) AS trips
            FROM users u
            LEFT JOIN trips t ON t.user_id = u.id
            GROUP BY u.id ORDER BY u.created_at DESC
        ')->fetchAll();
        if ($rows) $users = $rows;
    } catch (PDOException) {}
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
<div class="admin-wrap">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>

  <main class="main">
    <div class="topbar">
      <div>
        <div class="page-title">Usuarios</div>
        <div class="page-sub">Gestiona las cuentas de waydi · <?= count($users) ?> usuarios</div>
      </div>
      <button class="btn btn-primary" onclick="alert('Formulario nuevo usuario (en desarrollo)')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
        Nuevo usuario
      </button>
    </div>

    <div class="card">
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Usuario</th>
              <th>Email</th>
              <th>Rol</th>
              <th>Viajes</th>
              <th>Registrado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
              <td class="td-muted"><?= $u['id'] ?></td>
              <td>
                <div style="display:flex;align-items:center;gap:10px;">
                  <div style="width:34px;height:34px;border-radius:50%;background:var(--lavender);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--lila-deep);font-size:14px;flex-shrink:0;">
                    <?= mb_strtoupper(mb_substr($u['name'], 0, 1)) ?>
                  </div>
                  <span class="td-bold"><?= htmlspecialchars($u['name']) ?></span>
                </div>
              </td>
              <td class="td-muted"><?= htmlspecialchars($u['email']) ?></td>
              <td>
                <?php if ($u['is_admin']): ?>
                  <span class="badge badge-rosa">Admin</span>
                <?php else: ?>
                  <span class="badge badge-lila">Usuario</span>
                <?php endif; ?>
              </td>
              <td><span class="badge badge-green"><?= $u['trips'] ?? 0 ?></span></td>
              <td class="td-muted"><?= substr($u['created_at'] ?? '', 0, 10) ?></td>
              <td>
                <div style="display:flex;gap:6px;">
                  <button class="btn btn-ghost btn-sm" onclick="alert('Editar usuario #<?= $u['id'] ?>')">Editar</button>
                  <button class="btn btn-danger btn-sm" onclick="if(confirm('¿Eliminar usuario?')) alert('Eliminado')">Eliminar</button>
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
