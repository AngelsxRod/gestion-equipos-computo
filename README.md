# Proyecto-Papa — Sistema de Control de Cómputo

Sistema web para el control de inventario de computadoras (PC de escritorio y laptops) de la organización: asignaciones a empleados, reportes por área, reportes de garantías activas y reportes por año de adquisición.

Stack: **PHP puro (sin frameworks) + PDO + MariaDB**, con Tailwind CSS y Chart.js para la interfaz (ambos compilados/vendorizados de antemano, sin build step ni dependencias de red en producción).

## Requisitos

- PHP >= 8.1 con extensión `pdo_mysql`
- MariaDB >= 10.4

## Instalación

1. Clonar el repositorio y ubicarse en la raíz del proyecto.

2. Crear la base de datos y las tablas:

   ```bash
   mysql -u <usuario> -p < sql/schema.sql
   ```

3. Copiar la configuración de ejemplo y ajustar credenciales:

   ```bash
   cp config/config.example.php config/config.php
   ```

   Editar `config/config.php` con el host, usuario, contraseña y nombre de base de datos reales (usar un usuario de aplicación con privilegios sobre `control_computo`, no root).

4. Crear el usuario administrador inicial:

   ```bash
   php sql/seed.php
   ```

   El script pedirá nombre de usuario y contraseña por consola.

5. Levantar el servidor de desarrollo:

   ```bash
   php -S localhost:8000 -t public
   ```

6. Abrir `http://localhost:8000` e iniciar sesión con el usuario creado en el paso 4.

## Frontend (Tailwind CSS + Chart.js)

La interfaz usa Tailwind CSS (compilado con el CLI standalone, sin Node/npm) y Chart.js (vendorizado localmente). El CSS y el JS ya vienen compilados y versionados en el repo (`public/assets/css/app.css`, `public/assets/js/chart.min.js`), así que **no hace falta nada de esto para levantar el proyecto** — solo es necesario si vas a cambiar clases de Tailwind en las vistas.

Descargar las herramientas (una sola vez):

```bash
mkdir -p tools
curl -sLo tools/tailwindcss https://github.com/tailwindlabs/tailwindcss/releases/download/v3.4.17/tailwindcss-linux-x64
chmod +x tools/tailwindcss
curl -sLo public/assets/js/chart.min.js https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js
```

Recompilar el CSS tras cambiar clases en cualquier `.php` de `public/` o `src/`:

```bash
./tools/tailwindcss -i ./resources/css/app.css -o ./public/assets/css/app.css --minify
```

Para desarrollo con recompilación automática:

```bash
./tools/tailwindcss -i ./resources/css/app.css -o ./public/assets/css/app.css --watch
```

## Estructura

```
config/     Configuración y conexión PDO
src/        Autenticación, modelos de dominio, reportes y helpers
public/     Document root: páginas accesibles por HTTP
sql/        Esquema de base de datos y script de siembra del admin
resources/  Fuente de Tailwind CSS (resources/css/app.css)
tools/      Binario standalone de Tailwind (no versionado)
```

## Roles

- **admin**: acceso completo (crear/editar/eliminar áreas, empleados, equipos, asignaciones).
- **consulta**: solo lectura de listados, historial de asignaciones y reportes.

## Reportes disponibles

- Equipos por área.
- Equipos con garantía activa (vigente a la fecha).
- Equipos por año de adquisición.

Cada reporte se puede ver en pantalla (filtrable, con gráfico Chart.js) o exportar a CSV.
