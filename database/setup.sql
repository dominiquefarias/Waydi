-- Ejecutar como root: sudo mysql < database/setup.sql
-- Crea la base de datos, el usuario y ajusta la política de contraseñas

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS waydi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bajar política de contraseñas (entorno local)
SET GLOBAL validate_password.policy = LOW;
SET GLOBAL validate_password.length = 4;

-- Crear usuario (borra si ya existe de un intento anterior)
DROP USER IF EXISTS 'waydi'@'localhost';
CREATE USER 'waydi'@'localhost' IDENTIFIED BY 'waydi1234';
GRANT ALL PRIVILEGES ON waydi.* TO 'waydi'@'localhost';
FLUSH PRIVILEGES;

SELECT 'Usuario waydi creado correctamente.' AS resultado;
