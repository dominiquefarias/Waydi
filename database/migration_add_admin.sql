-- ============================================================
--  waydi · Migración: campo is_admin en users
-- ============================================================
USE waydi;

ALTER TABLE users
  ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0 AFTER email;

-- Para convertir un usuario en admin manualmente:
-- UPDATE users SET is_admin = 1 WHERE email = 'tuemail@example.com';
