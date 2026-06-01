-- ============================================================
--  waydi · Migración: añadir tabla trip_shares
--  Ejecutar si ya tenías la base de datos creada antes.
-- ============================================================
USE waydi;

CREATE TABLE IF NOT EXISTS trip_shares (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  trip_id     INT UNSIGNED NOT NULL,
  user_id     INT UNSIGNED NOT NULL,
  invited_by  INT UNSIGNED NOT NULL,
  role        ENUM('viewer','editor') NOT NULL DEFAULT 'editor',
  status      ENUM('pending','accepted','declined') NOT NULL DEFAULT 'pending',
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_trip_user (trip_id, user_id),
  FOREIGN KEY (trip_id)    REFERENCES trips(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id)    REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (invited_by) REFERENCES users(id) ON DELETE CASCADE
);
