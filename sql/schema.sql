-- ============================================================
-- Sistema de Control de Cómputo - Esquema de base de datos
-- Motor: MariaDB (InnoDB, utf8mb4)
-- ============================================================

CREATE DATABASE IF NOT EXISTS control_computo
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE control_computo;

-- ------------------------------------------------------------
-- usuarios: login del sistema (NO son "empleados")
-- ------------------------------------------------------------
CREATE TABLE usuarios (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario  VARCHAR(50)  NOT NULL,
    password_hash   VARCHAR(255) NOT NULL,
    rol             ENUM('admin','consulta') NOT NULL DEFAULT 'consulta',
    activo          TINYINT(1)   NOT NULL DEFAULT 1,
    creado_en       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                  ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_usuarios_nombre_usuario (nombre_usuario)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- areas: departamentos de la organización
-- ------------------------------------------------------------
CREATE TABLE areas (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(100) NOT NULL,
    activo          TINYINT(1)   NOT NULL DEFAULT 1,
    creado_en       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                  ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_areas_nombre (nombre)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- empleados
-- ------------------------------------------------------------
CREATE TABLE empleados (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(150) NOT NULL,
    codigo_empleado VARCHAR(30)  NULL,
    area_id         INT UNSIGNED NOT NULL,
    activo          TINYINT(1)   NOT NULL DEFAULT 1,
    creado_en       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                  ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_empleados_area
        FOREIGN KEY (area_id) REFERENCES areas(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    KEY idx_empleados_area_id (area_id),
    KEY idx_empleados_activo (activo)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- equipos: solo computadoras (laptop / escritorio).
-- Los datos de garantía viven embebidos en esta tabla porque la
-- relación es 1-a-1 con el ciclo de vida del equipo (no se pide
-- historial de garantías, solo si la vigente está activa).
-- ------------------------------------------------------------
CREATE TABLE equipos (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo                ENUM('laptop','escritorio') NOT NULL,
    marca               VARCHAR(60)  NOT NULL,
    modelo              VARCHAR(80)  NOT NULL,
    numero_serie        VARCHAR(100) NOT NULL,
    anio_adquisicion    SMALLINT UNSIGNED NOT NULL,
    estado              ENUM('activo','en_reparacion','de_baja') NOT NULL DEFAULT 'activo',
    garantia_proveedor  VARCHAR(120) NULL,
    garantia_inicio     DATE NULL,
    garantia_fin        DATE NULL,
    area_id             INT UNSIGNED NULL,
    activo              TINYINT(1)   NOT NULL DEFAULT 1,
    creado_en           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                      ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_equipos_numero_serie (numero_serie),
    CONSTRAINT fk_equipos_area
        FOREIGN KEY (area_id) REFERENCES areas(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    KEY idx_equipos_anio_adquisicion (anio_adquisicion),
    KEY idx_equipos_garantia_fin (garantia_fin),
    KEY idx_equipos_activo (activo)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- asignaciones: histórico completo.
-- La asignación ACTIVA de un equipo es la fila con fecha_devolucion IS NULL.
-- La regla "una sola asignación activa por equipo" se aplica a nivel de
-- aplicación (transacción con SELECT ... FOR UPDATE), no como constraint
-- SQL, porque MariaDB no soporta índices únicos parciales/filtrados.
-- ------------------------------------------------------------
CREATE TABLE asignaciones (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    equipo_id           INT UNSIGNED NOT NULL,
    empleado_id         INT UNSIGNED NOT NULL,
    fecha_asignacion    DATE NOT NULL,
    fecha_devolucion    DATE NULL,
    observaciones       VARCHAR(255) NULL,
    creado_por          INT UNSIGNED NULL,
    creado_en           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_asignaciones_equipo
        FOREIGN KEY (equipo_id) REFERENCES equipos(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_asignaciones_empleado
        FOREIGN KEY (empleado_id) REFERENCES empleados(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_asignaciones_usuario
        FOREIGN KEY (creado_por) REFERENCES usuarios(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    KEY idx_asignaciones_equipo_id (equipo_id),
    KEY idx_asignaciones_empleado_id (empleado_id),
    KEY idx_asignaciones_fecha_devolucion (fecha_devolucion)
) ENGINE=InnoDB;
