# waydi ✈️

<p align="center">
  <img src="https://img.shields.io/badge/Status-En%20Desarrollo-green?style=for-the-badge&logo=github" />
  <img src="https://img.shields.io/badge/Version-1.0-blue?style=for-the-badge" />
  <img src="https://img.shields.io/badge/React-19-61DAFB?style=for-the-badge&logo=react&logoColor=black" />
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
</p>

**waydi** es una aplicación web para planificar itinerarios de viaje de forma visual e intuitiva. Permite organizar actividades por días, visualizarlas en un mapa interactivo, añadir notas, y compartir el viaje con otros usuarios.

---

## 🚀 Características Principales

* **🗺️ Itinerario visual**: Organiza actividades por días con tarjetas de color por categoría (gastronomía, cultura, transporte, etc.)
* **📍 Mapa interactivo**: Pins numerados en el mapa sincronizados con las tarjetas de actividad
* **📝 Notas del día**: Notas adhesivas por día en tres tonos (lila, rosa, lavanda)
* **👥 Compartir viajes**: Invita a otras personas a ver o editar tu itinerario por email
* **🔒 Autenticación**: Registro, login y recuperación de contraseña por email
* **🛡️ Panel de administración**: Gestión completa de usuarios y viajes desde un panel dedicado

---

## 🛠️ Tecnologías Utilizadas

<p align="left">
  <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/react/react-original.svg" alt="react" width="40" height="40"/>
  <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/typescript/typescript-original.svg" alt="typescript" width="40" height="40"/>
  <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/php/php-original.svg" alt="php" width="40" height="40"/>
  <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/mysql/mysql-original-wordmark.svg" alt="mysql" width="40" height="40"/>
</p>

* **Frontend**: React 19 + TypeScript + Tailwind CSS v4 (con Vite)
* **Backend**: PHP 8.x con Apache (.htaccess rewrite)
* **Base de datos**: MySQL / MariaDB
* **Autenticación**: JWT (tokens de 7 días) + bcrypt para contraseñas
* **Correo**: PHPMailer vía SMTP
* **Tipografía**: Plus Jakarta Sans vía Google Fonts

---

## 📂 Estructura del Proyecto

```
waydi/
├── database/                   # SQL para la base de datos
│   ├── schema.sql              # Crea todas las tablas desde cero
│   ├── seed.sql                # Datos de ejemplo (viaje a París)
│   ├── migration_add_sharing.sql   # Añade tabla trip_shares (si ya tienes la DB)
│   └── migration_add_admin.sql     # Añade columna is_admin (si ya tienes la DB)
│
├── server/                     # Backend PHP
│   ├── index.php               # Router principal (entrada de todas las peticiones)
│   ├── mailer.php              # Envío de correos (PHPMailer)
│   ├── composer.json           # Dependencias PHP
│   ├── .htaccess               # Rewrite de Apache → index.php
│   ├── .env.example            # Plantilla de variables de entorno
│   ├── config/
│   │   ├── db.php              # Conexión PDO a MySQL
│   │   └── env.php             # Carga del archivo .env
│   ├── middleware/
│   │   ├── auth.php            # requireAuth() — verifica el JWT
│   │   └── admin.php           # requireAdmin() — verifica is_admin en DB
│   └── routes/
│       ├── auth.php            # /api/auth/* (register, login, me, reset)
│       ├── trips.php           # /api/trips/* (CRUD de viajes)
│       ├── shares.php          # /api/trips/:id/share, /api/invitations/*
│       └── admin.php           # /api/admin/* (stats, usuarios, viajes)
│
├── src/                        # Frontend React
│   ├── main.tsx                # Punto de entrada (BrowserRouter + AuthProvider)
│   ├── App.tsx                 # Rutas y guardas de autenticación
│   ├── index.css               # Estilos globales + tokens de diseño (Tailwind v4)
│   ├── context/
│   │   └── AuthContext.tsx     # Estado de sesión global (useAuth hook)
│   ├── lib/
│   │   ├── api.ts              # Cliente HTTP centralizado (todas las llamadas a la API)
│   │   └── categories.ts       # Colores y estilos por categoría de actividad
│   ├── data/
│   │   └── trip.ts             # Datos de demo del itinerario (París)
│   ├── components/             # Componentes del itinerario
│   │   ├── TopBar.tsx          # Barra superior con logo y usuario
│   │   ├── TripHero.tsx        # Cabecera del viaje (título, fechas, tabs de días)
│   │   ├── DayTabs.tsx         # Pestañas de selección de día
│   │   ├── Timeline.tsx        # Columna de actividades del día
│   │   ├── ActivityCard.tsx    # Tarjeta individual de actividad
│   │   ├── MapPanel.tsx        # Mapa con pins numerados
│   │   ├── NotesPanel.tsx      # Notas adhesivas del día
│   │   └── Icons.tsx           # Todos los iconos SVG de la app
│   └── pages/                  # Páginas completas
│       ├── LoginPage.tsx
│       ├── RegisterPage.tsx
│       ├── ForgotPasswordPage.tsx
│       ├── ResetPasswordPage.tsx
│       └── AdminPage.tsx       # Panel de administración
│
├── public/                     # Archivos estáticos
├── index.html                  # HTML raíz (carga la fuente y el bundle)
├── vite.config.ts              # Configuración de Vite
├── package.json                # Dependencias y scripts npm
└── .env.example                # Plantilla de variables de entorno del frontend
```

---

## ⚙️ Instalación y Configuración

### 1. Base de datos

Importa el schema en MySQL:

```sql
source database/schema.sql
source database/seed.sql   -- opcional, carga datos de ejemplo
```

Para hacer admin a un usuario:

```sql
UPDATE users SET is_admin = 1 WHERE email = 'tu@email.com';
```

### 2. Backend PHP

```bash
cd server
cp .env.example .env          # Rellena DB_*, JWT_SECRET y SMTP_*
composer install              # Instala dependencias PHP
```

Apunta Apache/Nginx a la carpeta `server/` como document root. El `.htaccess` redirige todo a `index.php`.

### 3. Frontend React

```bash
cp .env.example .env          # Ajusta VITE_API_URL a tu backend
npm install
npm run dev                   # Servidor de desarrollo en http://localhost:5173
npm run build                 # Build de producción en dist/
```

---

## 🌐 Variables de Entorno

### Frontend (`.env`)

| Variable | Descripción | Ejemplo |
|---|---|---|
| `VITE_API_URL` | URL del backend PHP | `http://localhost:4000` |

### Backend (`server/.env`)

| Variable | Descripción |
|---|---|
| `DB_HOST` / `DB_PORT` / `DB_NAME` | Conexión a MySQL |
| `DB_USER` / `DB_PASSWORD` | Credenciales de MySQL |
| `JWT_SECRET` | Clave secreta para firmar tokens (mín. 32 chars) |
| `SMTP_HOST` / `SMTP_PORT` | Servidor de correo |
| `SMTP_USER` / `SMTP_PASS` | Credenciales SMTP |
| `SMTP_FROM_MAIL` / `SMTP_FROM_NAME` | Remitente de los correos |
| `FRONTEND_URL` | URL del frontend (para CORS y links en emails) |

---

## 🗄️ Esquema de Base de Datos

| Tabla | Descripción |
|---|---|
| `users` | Usuarios con `is_admin` para el panel de administración |
| `trips` | Viajes (título, ciudad, fechas, nº de viajeros) |
| `days` | Días de un viaje con etiqueta y fecha |
| `activities` | Actividades de un día (hora, duración, categoría, pin en mapa) |
| `notes` | Notas adhesivas de un día (tono de color, título, texto) |
| `trip_shares` | Invitaciones para compartir viajes (rol: viewer/editor, estado: pending/accepted/declined) |

---

<p align="center">
  <b>Desarrollado por Dominique Farías Osorio</b>
</p>
