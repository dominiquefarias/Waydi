<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-box">W</div>
    <div>
      <div class="logo-text">waydi</div>
      <div class="logo-badge">Admin</div>
    </div>
  </div>

  <?php $page = basename($_SERVER['PHP_SELF'], '.php'); ?>

  <div class="nav-section-label">Principal</div>
  <a href="dashboard.php" class="nav-link <?= $page === 'dashboard' ? 'active' : '' ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
    Dashboard
  </a>
  <a href="trips.php" class="nav-link <?= $page === 'trips' ? 'active' : '' ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
    Viajes
  </a>
  <a href="users.php" class="nav-link <?= $page === 'users' ? 'active' : '' ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    Usuarios
  </a>

  <div class="nav-section-label">Configuración</div>
  <a href="#" class="nav-link">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M4.93 19.07l1.41-1.41M19.07 19.07l-1.41-1.41M12 2v2M12 20v2M2 12h2M20 12h2"/></svg>
    Ajustes
  </a>

  <div class="sidebar-footer">
    <a href="dashboard.php?logout=1" class="nav-link" onclick="return confirm('¿Cerrar sesión?')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      Cerrar sesión
    </a>
  </div>
</aside>
