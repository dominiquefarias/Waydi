# waydi ✈️

<p align="center">
  <img src="https://img.shields.io/badge/Status-En%20Desarrollo-green?style=for-the-badge&logo=github" />
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
  <img src="https://img.shields.io/badge/Apache-2.x-D22128?style=for-the-badge&logo=apache&logoColor=white" />
</p>

**waydi** es una aplicación web para planificar itinerarios de viaje de forma visual. Organiza actividades por días, las muestra en un mapa interactivo y permite añadir notas y compartir el viaje con otros usuarios.

Construida con **PHP + HTML + CSS + JavaScript** puro — sin frameworks, sin npm, sin build tools.

---

## 🚀 Características

* 🗺️ Itinerario visual con actividades por días
* 📍 Mapa con pins numerados sincronizados con las actividades
* 📝 Notas adhesivas por día en tres colores
* 👥 Compartir viajes con otros usuarios
* 🔒 Registro, login y recuperación de contraseña
* 🛡️ Panel de administración para gestionar usuarios y viajes

---

## 🛠️ Tecnologías

* **Backend**: PHP 8.x con PDO
* **Base de datos**: MySQL / MariaDB
* **Servidor web**: Apache con mod_rewrite
* **Frontend**: HTML5 + CSS3 + JavaScript vanilla
* **Tipografía**: Plus Jakarta Sans (Google Fonts)

---

## 📂 Estructura

```
Waydi/
├── config/
│   ├── db.php              ← Conexión a MySQL + carga del .env
│   └── funciones.php       ← Helpers de sesión, auth, email, iconos
├── css/
│   ├── auth.css            ← Login, registro, recuperar contraseña
│   └── app.css             ← Itinerario, admin, lista de viajes
├── js/
│   ├── itinerario.js       ← Cambio de días y selección actividad/pin
│   └── admin.js            ← Navegación por tabs del admin
├── admin/
│   └── index.php           ← Panel de administración
├── database/
│   ├── schema.sql          ← Crea todas las tablas desde cero
│   ├── seed.sql            ← Datos de ejemplo (viaje a París)
│   ├── migration_add_sharing.sql
│   └── migration_add_admin.sql
├── index.php               ← Lista de viajes del usuario
├── itinerario.php          ← Vista del itinerario (?id=X)
├── nuevo-viaje.php         ← Crear un nuevo viaje
├── login.php
├── registro.php
├── logout.php
├── recuperar.php           ← Recuperación de contraseña por email
├── restablecer.php         ← Formulario de nueva contraseña
└── .env.example
```

---

## ⚙️ Instalación

### 1. Base de datos

```sql
-- Crear la base de datos desde cero
source database/schema.sql;

-- (Opcional) Cargar datos de ejemplo
source database/seed.sql;
```

### 2. Variables de entorno

```bash
cp .env.example .env
# Edita .env y rellena DB_HOST, DB_USER, DB_PASSWORD, DB_NAME
```

### 3. Apache

Apunta el DocumentRoot al directorio del proyecto:

```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /ruta/al/proyecto/Waydi

    <Directory /ruta/al/proyecto/Waydi>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Activa el módulo de rewrite si no lo tienes:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 4. Primer administrador

```sql
UPDATE users SET is_admin = 1 WHERE email = 'tu@email.com';
```

Luego accede a `http://localhost/admin/`.

---

## 🗄️ Tablas de la base de datos

| Tabla | Descripción |
|---|---|
| `users` | Usuarios (con `is_admin` para el panel) |
| `trips` | Viajes (título, ciudad, fechas, viajeros) |
| `days` | Días de cada viaje |
| `activities` | Actividades con hora, categoría y posición en el mapa |
| `notes` | Notas adhesivas por día (lila / rosa / lavender) |
| `trip_shares` | Invitaciones para compartir viajes |

---

<p align="center">
  <b>Desarrollado por Dominique Farías Osorio</b>
</p>
