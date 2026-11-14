-- ============================================================
-- GESTOR Y VISUALIZADOR DE HABITOS PERSONALES
-- Motor: MariaDB 10.4+ (XAMPP)
-- Archivo: GestorHabitos_MariaDB.sql

-- ============================================================

CREATE DATABASE IF NOT EXISTS GestorHabitos
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE GestorHabitos;

-- ------------------------------------------------------------
-- 1. USUARIOS
-- Cada cuenta tiene sus propios habitos.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Usuarios (
    id_usuario INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(254) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    activo TINYINT(1) NOT NULL DEFAULT 1,

    CONSTRAINT PK_Usuarios PRIMARY KEY (id_usuario),
    CONSTRAINT UQ_Usuarios_Correo UNIQUE (correo),
    CONSTRAINT CK_Usuarios_Nombre
        CHECK (CHAR_LENGTH(TRIM(nombre)) > 0),
    CONSTRAINT CK_Usuarios_Correo
        CHECK (CHAR_LENGTH(TRIM(correo)) > 0),
    CONSTRAINT CK_Usuarios_Activo
        CHECK (activo IN (0, 1))
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. CATEGORIAS
-- Catalogo compartido por los usuarios, como en el modelo
-- original. Un habito puede no tener categoria.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Categorias (
    id_categoria INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,

    CONSTRAINT PK_Categorias PRIMARY KEY (id_categoria),
    CONSTRAINT UQ_Categorias_Nombre UNIQUE (nombre),
    CONSTRAINT CK_Categorias_Nombre
        CHECK (CHAR_LENGTH(TRIM(nombre)) > 0)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. ETIQUETAS
-- Catalogo compartido. Un habito puede tener varias etiquetas.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Etiquetas (
    id_etiqueta INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,

    CONSTRAINT PK_Etiquetas PRIMARY KEY (id_etiqueta),
    CONSTRAINT UQ_Etiquetas_Nombre UNIQUE (nombre),
    CONSTRAINT CK_Etiquetas_Nombre
        CHECK (CHAR_LENGTH(TRIM(nombre)) > 0)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. HABITOS
-- Cada habito pertenece obligatoriamente a un usuario.
-- ON DELETE CASCADE elimina sus cumplimientos y etiquetas
-- asociadas cuando se elimina el habito.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Habitos (
    id_habito INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_usuario INT UNSIGNED NOT NULL,
    id_categoria INT UNSIGNED NULL,
    nombre VARCHAR(150) NOT NULL,
    color CHAR(7) NOT NULL DEFAULT '#4F46E5',
    fecha_creacion DATE NOT NULL DEFAULT (CURRENT_DATE),

    CONSTRAINT PK_Habitos PRIMARY KEY (id_habito),
    CONSTRAINT CK_Habitos_Nombre
        CHECK (CHAR_LENGTH(TRIM(nombre)) > 0),
    CONSTRAINT CK_Habitos_Color
        CHECK (
            CHAR_LENGTH(color) = 7
            AND LEFT(color, 1) = '#'
            AND SUBSTRING(color, 2) REGEXP '^[0-9A-Fa-f]{6}$'
        ),

    CONSTRAINT FK_Habitos_Usuarios
        FOREIGN KEY (id_usuario)
        REFERENCES Usuarios (id_usuario)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT FK_Habitos_Categorias
        FOREIGN KEY (id_categoria)
        REFERENCES Categorias (id_categoria)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    INDEX IX_Habitos_Usuario (id_usuario),
    INDEX IX_Habitos_Categoria (id_categoria),
    INDEX IX_Habitos_Usuario_Fecha (id_usuario, fecha_creacion)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. CUMPLIMIENTOS
-- Un habito solo puede tener un cumplimiento por fecha.
-- Las rachas y estadisticas se calculan desde PHP consultando
-- estas fechas; no hace falta almacenar una racha redundante.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Cumplimientos (
    id_cumplimiento INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_habito INT UNSIGNED NOT NULL,
    fecha DATE NOT NULL,

    CONSTRAINT PK_Cumplimientos PRIMARY KEY (id_cumplimiento),
    CONSTRAINT UQ_Cumplimiento_Diario UNIQUE (id_habito, fecha),

    CONSTRAINT FK_Cumplimientos_Habitos
        FOREIGN KEY (id_habito)
        REFERENCES Habitos (id_habito)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    INDEX IX_Cumplimientos_Fecha (fecha)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. HABITO_ETIQUETAS
-- Tabla intermedia para la relacion muchos a muchos.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS HabitoEtiquetas (
    id_habito INT UNSIGNED NOT NULL,
    id_etiqueta INT UNSIGNED NOT NULL,

    CONSTRAINT PK_HabitoEtiquetas
        PRIMARY KEY (id_habito, id_etiqueta),

    CONSTRAINT FK_HabitoEtiquetas_Habitos
        FOREIGN KEY (id_habito)
        REFERENCES Habitos (id_habito)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT FK_HabitoEtiquetas_Etiquetas
        FOREIGN KEY (id_etiqueta)
        REFERENCES Etiquetas (id_etiqueta)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    INDEX IX_HabitoEtiquetas_Etiqueta (id_etiqueta)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. DATOS INICIALES OPCIONALES
-- Categorias y etiquetas de ejemplo.
-- INSERT IGNORE evita duplicados si se ejecuta otra vez.
-- Puedes quitar este bloque si prefieres catalogos vacios.
-- ------------------------------------------------------------
INSERT IGNORE INTO Categorias (nombre) VALUES
    ('Salud'),
    ('Ejercicio'),
    ('Estudio'),
    ('Productividad'),
    ('Bienestar');

INSERT IGNORE INTO Etiquetas (nombre) VALUES
    ('Mañana'),
    ('Tarde'),
    ('Noche'),
    ('Diario');

-- ------------------------------------------------------------
-- 8. VERIFICACION
-- Deben aparecer las seis tablas del proyecto.
-- ------------------------------------------------------------
SELECT
    TABLE_NAME AS Nombre_Tabla
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_TYPE = 'BASE TABLE'
ORDER BY TABLE_NAME;
