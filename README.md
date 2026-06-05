# Inmobiliaria

Sitio web inmobiliario en PHP + MySQL con panel de administración, catálogo público de propiedades y estilos compilados con Tailwind CSS.

## Stack

- PHP
- MySQL
- XAMPP / Apache
- Tailwind CSS
- Node.js solo para compilar estilos

## Qué incluye

- Home pública con hero en video, propiedades destacadas y staff.
- Listado de propiedades con filtros.
- Vista individual de propiedad.
- Mapa y página de contacto.
- Panel admin con login.
- ABM de propiedades.
- Exportación de propiedades a CSV.
- ABM de íconos/características.
- ABM de staff.
- ABM de usuarios administradores con permisos por sección.
- Configuración de datos visibles del home y footer.

## Estructura

```text
admin/      Panel de administración
backend/    Conexión, helpers, auth y acciones CRUD
public/     Frontend público, assets, imágenes, video y CSS compilado
sql/        Esquema base y scripts SQL complementarios
input.css   Entrada de Tailwind
```

## Requisitos

- XAMPP con Apache y MySQL
- PHP con PDO MySQL habilitado
- Node.js y npm

## Instalación

1. Copiar el proyecto dentro de `C:\xampp\htdocs\inmobiliaria`.
2. Crear la base de datos `inmobiliaria`.
3. Importar `sql/schema.sql`.
4. Verificar la conexión en `backend/conexion.php`.
5. Instalar dependencias frontend:

```bash
npm install
```

6. Compilar CSS:

```bash
npm run build:css
```

7. Levantar Apache y MySQL desde XAMPP.

## Base de datos

La conexión actual en `backend/conexion.php` usa:

- Host: `localhost`
- Base: `inmobiliaria`
- Usuario: `root`
- Password: vacío

`sql/schema.sql` ya crea las tablas principales:

- `usuarios`
- `usuario_permisos`
- `operaciones`
- `tipos_propiedad`
- `estados_propiedad`
- `iconos_caracteristica`
- `staff`
- `site_settings`
- `propiedades`
- `propiedad_caracteristicas`
- `propiedad_imagenes`

Los archivos dentro de `sql/` con fecha funcionan como scripts históricos/complementarios del desarrollo.

## Acceso

- Sitio público: `http://localhost/inmobiliaria/public/`
- Login admin: `http://localhost/inmobiliaria/admin/login.php`

Credenciales demo iniciales:

- Email: `admin@inmobiliaria.com`
- Contraseña: `123456`

## Scripts npm

```bash
npm run build:css   # compila public/css/tailwind.css
npm run dev:css     # recompila en modo watch
```

## Notas de uso

- El panel administra permisos por sección: propiedades, íconos, staff, usuarios y configuraciones.
- El video principal del home se guarda en `public/video`.
- Las imágenes públicas e imágenes de propiedades se sirven desde `public/img`.
- El proyecto no usa variables de entorno; la configuración está en archivos PHP.
- No hay suite de tests automatizados configurada en `package.json`.

## Sugerencia de arranque rápido

1. Importar `sql/schema.sql`.
2. Ejecutar `npm run build:css`.
3. Abrir `http://localhost/inmobiliaria/public/`.
4. Entrar al panel admin y cargar propiedades reales, staff y configuraciones.
