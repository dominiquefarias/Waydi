<?php
function renderSidebar(string $active): void {
    $nav = [
        ['href' => 'dashboard.php', 'icon' => '◈', 'label' => 'Dashboard'],
        ['href' => 'trips.php',     'icon' => '✈', 'label' => 'Viajes'],
        ['href' => 'users.php',     'icon' => '👤', 'label' => 'Usuarios'],
    ];
    ?>
    <aside class="sidebar">
      <div class="sidebar-logo">
        <div class="logo-icon">W</div>
        <span class="logo-text">waydi</span>
        <span class="logo-badge">Admin</span>
      </div>
      <nav class="sidebar-nav">
        <div class="nav-label">Menú</div>
        <?php foreach ($nav as $item): ?>
          <a href="<?= $item['href'] ?>" class="nav-item <?= $active === $item['href'] ? 'active' : '' ?>">
            <span class="nav-icon"><?= $item['icon'] ?></span>
            <?= $item['label'] ?>
          </a>
        <?php endforeach; ?>
      </nav>
      <div class="sidebar-footer">
        <a href="logout.php" class="nav-item" style="color:#C0567E;">
          <span class="nav-icon">⏻</span> Cerrar sesión
        </a>
      </div>
    </aside>
    <?php
}
