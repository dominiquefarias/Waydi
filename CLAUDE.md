# waydi — Guía para Claude Code

## Qué es este proyecto

App de itinerarios de viaje. Frontend en React + TypeScript + Tailwind CSS v4, backend en PHP con Apache, base de datos MySQL.

## Cómo correr el proyecto

### Frontend
```bash
npm install
npm run dev        # http://localhost:5173
npm run build      # build de producción → dist/
```

### Backend
```bash
cd server
composer install
# Configura server/.env con DB_* y JWT_SECRET
# Apache debe apuntar a /server como document root
```

### Base de datos
```bash
# Instalación desde cero:
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql   # datos de ejemplo

# Si la DB ya existe y solo faltan las migraciones:
mysql -u root -p < database/migration_add_sharing.sql
mysql -u root -p < database/migration_add_admin.sql
```

## Arquitectura

```
MySQL ←→ PHP (server/) ←→ React (src/) ← Usuario
```

- El frontend llama a la API vía `src/lib/api.ts` — **todas las llamadas HTTP pasan por ahí**
- El backend tiene un único punto de entrada: `server/index.php` (router)
- JWT en `localStorage` como `waydi_token`, enviado como `Authorization: Bearer <token>`

## Rutas del backend (server/)

| Ruta | Archivo | Descripción |
|---|---|---|
| `/api/auth/*` | `routes/auth.php` | register, login, me, forgot-password, reset-password |
| `/api/trips/*` | `routes/trips.php` | CRUD de viajes |
| `/api/trips/:id/share` | `routes/shares.php` | Compartir viaje, gestionar miembros |
| `/api/invitations/*` | `routes/shares.php` | Aceptar/rechazar invitaciones |
| `/api/admin/*` | `routes/admin.php` | Panel de administración (requiere is_admin=1) |

## Rutas del frontend (src/)

| Ruta | Componente | Acceso |
|---|---|---|
| `/login` | `pages/LoginPage.tsx` | Público |
| `/register` | `pages/RegisterPage.tsx` | Público |
| `/forgot-password` | `pages/ForgotPasswordPage.tsx` | Público |
| `/reset-password` | `pages/ResetPasswordPage.tsx` | Público |
| `/` | `ItineraryView` en `App.tsx` | Requiere login |
| `/admin` | `pages/AdminPage.tsx` | Requiere `is_admin = 1` |

## Añadir un nuevo endpoint PHP

1. Elige el archivo de ruta correcto en `server/routes/`
2. Añade un bloque `if ($action === '...' && $method === '...')` 
3. Usa `requireAuth()` si necesita login, `requireAdmin($pdo)` si es solo para admins
4. Si es una ruta completamente nueva, añade el patrón regex en `server/index.php`

## Añadir una nueva página React

1. Crea el archivo en `src/pages/NombrePage.tsx`
2. Añade el `<Route>` en `src/App.tsx`
3. Si necesita login, envuelve con `<RequireAuth>` (o `<RequireAdmin>` para admins)
4. Las llamadas a la API van en `src/lib/api.ts`

## Tokens de diseño (Tailwind v4)

Definidos en `src/index.css` con `@theme`:

| Token | Color | Uso |
|---|---|---|
| `--color-ink` | `#332E45` | Texto principal |
| `--color-muted` | `#807A95` | Texto secundario |
| `--color-faint` | `#A9A3BC` | Texto desactivado |
| `--color-lila` | `#DCD0FF` | Acentos principales |
| `--color-lila-deep` | `#B49BF0` | Hover / activo |
| `--color-lavender` | `#ECE6FB` | Fondos suaves |
| `--color-rosa` | `#FBD8E8` | Notas rosa |
| `--color-canvas` | `#F6F4FC` | Fondo general |

Clases de sombra especiales: `.soft`, `.soft-sm`, `.soft-lift` (sombras púrpura suaves).

## Variables de entorno

- **Frontend** (`.env`): `VITE_API_URL` — URL del backend PHP (default: `http://localhost:4000`)
- **Backend** (`server/.env`): `DB_*`, `JWT_SECRET`, `SMTP_*`, `FRONTEND_URL`

## Hacer a alguien administrador

```sql
UPDATE users SET is_admin = 1 WHERE email = 'correo@ejemplo.com';
```

Luego accede a `/admin` en el frontend.

## Notas importantes

- Tailwind v4 usa `@import "tailwindcss"` y `@theme {}` — **no hay `tailwind.config.ts`**
- El plugin es `@tailwindcss/vite`, no PostCSS
- PHP usa PDO para todas las consultas (sin mysqli)
- Los tokens JWT expiran en 7 días
- El endpoint `forgot-password` devuelve siempre el mismo mensaje (no revela si el email existe)
