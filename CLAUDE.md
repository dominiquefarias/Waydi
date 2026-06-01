# waydi — Guía para Claude Code

## Qué es este proyecto

App de itinerarios de viaje. PHP puro + HTML + CSS + JavaScript vanilla. Sin frameworks, sin npm, sin build tools.

## Estructura de archivos

```
Waydi/
├── config/
│   ├── db.php          ← Conexión PDO a MySQL + carga del .env
│   └── funciones.php   ← Helpers: sesión, auth, flash, escaping, iconos
├── css/
│   ├── auth.css        ← Estilos de login/registro/recuperar
│   └── app.css         ← Estilos del itinerario, admin y listas
├── js/
│   ├── itinerario.js   ← Cambio de días, selección actividad/pin
│   └── admin.js        ← Tabs del panel de admin
├── admin/
│   └── index.php       ← Panel de administración (solo is_admin=1)
├── database/
│   ├── schema.sql      ← Crea todas las tablas desde cero
│   ├── seed.sql        ← Datos de ejemplo (París)
│   ├── migration_add_sharing.sql
│   └── migration_add_admin.sql
├── index.php           ← Lista de viajes del usuario
├── itinerario.php      ← Vista de un viaje (?id=X)
├── login.php
├── registro.php
├── logout.php
├── recuperar.php       ← Envía email con token de reset
├── restablecer.php     ← Formulario para nueva contraseña (?token=X)
├── nuevo-viaje.php     ← Formulario para crear un viaje
├── .htaccess           ← Deshabilita listado de directorios
└── .env                ← Variables de entorno (no está en git)
```

## Cómo arrancar (Ubuntu + Apache + MySQL)

```bash
# 1. Clonar en Apache
cd /var/www/html && git clone https://github.com/dominiquefarias/Waydi.git

# 2. Crear BD y usuario MySQL (una sola vez)
sudo mysql < /var/www/html/Waydi/database/setup.sql
mysql -u waydi -pwaydi1234 waydi < /var/www/html/Waydi/database/schema.sql
mysql -u waydi -pwaydi1234 waydi < /var/www/html/Waydi/database/seed.sql

# 3. Variables de entorno
cp .env.example .env   # ya viene preconfigurado

# 4. Apache apuntando al proyecto (en 000-default.conf):
#    DocumentRoot /var/www/html/Waydi
#    <Directory /var/www/html/Waydi> AllowOverride All </Directory>
sudo a2enmod rewrite && sudo systemctl restart apache2
```

No hay `npm install`, no hay `composer install`, no hay build.

## Variables de entorno (.env)

| Variable | Para qué |
|---|---|
| DB_HOST / DB_PORT / DB_NAME | Conexión MySQL |
| DB_USER / DB_PASSWORD | Credenciales MySQL |
| MAIL_* | Configuración SMTP para correos |
| APP_URL | URL base de la app (para links en emails) |

## Autenticación

- PHP sessions (`$_SESSION['usuario']`)
- `requireLogin()` en `config/funciones.php` — redirige a login si no hay sesión
- `requireAdmin()` — además comprueba `is_admin = 1`
- Contraseñas con `password_hash()` + `password_verify()` (bcrypt)
- Recuperación de contraseña: token en DB + `mail()` de PHP

## Hacer a alguien administrador

```sql
UPDATE users SET is_admin = 1 WHERE email = 'correo@ejemplo.com';
```

Luego accede a `/admin/`.

## Añadir una nueva página

1. Crea el archivo PHP en la raíz (ej. `mi-pagina.php`)
2. Incluye al principio:
   ```php
   require_once __DIR__ . '/config/db.php';
   require_once __DIR__ . '/config/funciones.php';
   $usuario = requireLogin(); // o requireAdmin()
   $pdo = getDB();
   ```
3. Usa `e()` para escapar todo lo que imprimas del usuario
4. Usa `setFlash('exito'|'error', 'mensaje')` para mensajes entre redirecciones
5. Enlaza `/css/app.css` (o `/css/auth.css` para páginas de auth)

## Tokens de diseño (CSS variables en app.css y auth.css)

| Variable | Color | Uso |
|---|---|---|
| `--ink` | `#332E45` | Texto principal |
| `--muted` | `#807A95` | Texto secundario |
| `--faint` | `#A9A3BC` | Texto desactivado |
| `--lila` | `#DCD0FF` | Acentos, tabs activos |
| `--lila-deep` | `#B49BF0` | Botones primarios, hover |
| `--lavender` | `#ECE6FB` | Fondos suaves |
| `--rosa` | `#FBD8E8` | Notas rosa |
| `--canvas` | `#F6F4FC` | Fondo general |
