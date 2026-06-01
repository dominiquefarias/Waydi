-- ============================================================
--  waydi · Esquema MySQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS waydi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE waydi;

-- ------------------------------------------------------------
-- Usuarios
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id              INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
  name            VARCHAR(100)     NOT NULL,
  email           VARCHAR(255)     NOT NULL UNIQUE,
  password_hash   VARCHAR(255)     NOT NULL,
  reset_token     VARCHAR(255)     DEFAULT NULL,
  reset_expires   DATETIME         DEFAULT NULL,
  created_at      TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP        DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- Viajes
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS trips (
  id          INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
  user_id     INT UNSIGNED  NOT NULL,
  title       VARCHAR(255)  NOT NULL,
  subtitle    TEXT,
  date_range  VARCHAR(100),
  city        VARCHAR(150),
  travelers   TINYINT UNSIGNED DEFAULT 1,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Días de cada viaje
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS days (
  id        INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
  trip_id   INT UNSIGNED  NOT NULL,
  position  TINYINT UNSIGNED NOT NULL,   -- orden dentro del viaje
  label     VARCHAR(50),                 -- "Día 1"
  weekday   VARCHAR(20),                 -- "Jue"
  date      VARCHAR(50),                 -- "12 Jun"
  theme     VARCHAR(255),                -- "Llegada & íconos"
  FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Actividades de cada día
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS activities (
  id        INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
  day_id    INT UNSIGNED  NOT NULL,
  position  TINYINT UNSIGNED NOT NULL,
  time      VARCHAR(10),       -- "09:00"
  duration  VARCHAR(20),       -- "1.5 h"
  name      VARCHAR(255) NOT NULL,
  place     VARCHAR(255),
  category  ENUM('Cultura','Gastronomía','Ocio','Naturaleza','Transporte','Compras') NOT NULL,
  icon      VARCHAR(50),
  pin_x     FLOAT,             -- posición % en el mapa (eje X)
  pin_y     FLOAT,             -- posición % en el mapa (eje Y)
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (day_id) REFERENCES days(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Notas de cada día
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS notes (
  id        INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
  day_id    INT UNSIGNED  NOT NULL,
  position  TINYINT UNSIGNED NOT NULL,
  icon      VARCHAR(50),
  tone      ENUM('lila','rosa','lavender') NOT NULL DEFAULT 'lila',
  title     VARCHAR(255),
  text      TEXT,
  FOREIGN KEY (day_id) REFERENCES days(id) ON DELETE CASCADE
);
