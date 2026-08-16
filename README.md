# Proyecto-Papa — Sistema de Control de Cómputo

Sistema web para el control de inventario de computadoras (PC de escritorio y laptops) de la organización: asignaciones a empleados, reportes por área, reportes de garantías activas y reportes por año de adquisición.

Stack: **PHP puro (sin frameworks) + PDO + MariaDB**, sin dependencias externas.

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

## Estructura

```
config/     Configuración y conexión PDO
src/        Autenticación, modelos de dominio, reportes y helpers
public/     Document root: páginas accesibles por HTTP
sql/        Esquema de base de datos y script de siembra del admin
```

## Roles

- **admin**: acceso completo (crear/editar/eliminar áreas, empleados, equipos, asignaciones).
- **consulta**: solo lectura de listados, historial de asignaciones y reportes.

## Reportes disponibles

- Equipos por área.
- Equipos con garantía activa (vigente a la fecha).
- Equipos por año de adquisición.

Cada reporte se puede ver en pantalla (filtrable) o exportar a CSV.
