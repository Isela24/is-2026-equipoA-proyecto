-- BASE DE DATOS: GESTOR DE HABITOS PERSONALES
-- Microsoft SQL Server

-- 1. CREAR BASE DE DATOS

IF DB_ID('GestorHabitos') IS NULL
BEGIN
    CREATE DATABASE GestorHabitos;
END;
GO

USE GestorHabitos;
GO


-- 2. TABLA USUARIOS

CREATE TABLE Usuarios (
    id_usuario INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(100) NOT NULL,

    CONSTRAINT CK_Usuarios_Nombre
    CHECK (LEN(LTRIM(RTRIM(nombre))) > 0)
);
GO


-- 3. TABLA CATEGORIAS

CREATE TABLE Categorias (
    id_categoria INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(100) NOT NULL,

    CONSTRAINT UQ_Categorias_Nombre
    UNIQUE (nombre),

    CONSTRAINT CK_Categorias_Nombre
    CHECK (LEN(LTRIM(RTRIM(nombre))) > 0)
);
GO


-- 4. TABLA ETIQUETAS

CREATE TABLE Etiquetas (
    id_etiqueta INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(100) NOT NULL,

    CONSTRAINT UQ_Etiquetas_Nombre
    UNIQUE (nombre),

    CONSTRAINT CK_Etiquetas_Nombre
    CHECK (LEN(LTRIM(RTRIM(nombre))) > 0)
);
GO


-- 5. TABLA HABITOS

CREATE TABLE Habitos (
    id_habito INT IDENTITY(1,1) PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_categoria INT NULL,
    nombre NVARCHAR(150) NOT NULL,
    color CHAR(7) NOT NULL DEFAULT '#4F46E5',
    fecha_creacion DATE NOT NULL
        DEFAULT CAST(GETDATE() AS DATE),

    CONSTRAINT CK_Habitos_Nombre
    CHECK (LEN(LTRIM(RTRIM(nombre))) > 0),

    CONSTRAINT CK_Habitos_Color
    CHECK (
        color LIKE
        '#[0-9A-Fa-f][0-9A-Fa-f][0-9A-Fa-f][0-9A-Fa-f][0-9A-Fa-f][0-9A-Fa-f]'
    ),

    CONSTRAINT FK_Habitos_Usuarios
    FOREIGN KEY (id_usuario)
    REFERENCES Usuarios(id_usuario)
    ON DELETE CASCADE,

    CONSTRAINT FK_Habitos_Categorias
    FOREIGN KEY (id_categoria)
    REFERENCES Categorias(id_categoria)
    ON DELETE SET NULL
);
GO


-- 6. TABLA CUMPLIMIENTOS

CREATE TABLE Cumplimientos (
    id_cumplimiento INT IDENTITY(1,1) PRIMARY KEY,
    id_habito INT NOT NULL,
    fecha DATE NOT NULL,

    CONSTRAINT FK_Cumplimientos_Habitos
    FOREIGN KEY (id_habito)
    REFERENCES Habitos(id_habito)
    ON DELETE CASCADE,

    CONSTRAINT UQ_Cumplimiento_Diario
    UNIQUE (id_habito, fecha)
);
GO


-- 7. TABLA HABITO_ETIQUETAS

CREATE TABLE HabitoEtiquetas (
    id_habito INT NOT NULL,
    id_etiqueta INT NOT NULL,

    CONSTRAINT PK_HabitoEtiquetas
    PRIMARY KEY (id_habito, id_etiqueta),

    CONSTRAINT FK_HabitoEtiquetas_Habitos
    FOREIGN KEY (id_habito)
    REFERENCES Habitos(id_habito)
    ON DELETE CASCADE,

    CONSTRAINT FK_HabitoEtiquetas_Etiquetas
    FOREIGN KEY (id_etiqueta)
    REFERENCES Etiquetas(id_etiqueta)
    ON DELETE CASCADE
);
GO


-- 8. INDICES PARA OPTIMIZAR CONSULTAS

CREATE INDEX IX_Habitos_Usuario
ON Habitos(id_usuario);
GO

CREATE INDEX IX_Habitos_Categoria
ON Habitos(id_categoria);
GO


-- 9. VERIFICACION DE TABLAS CREADAS

SELECT
    TABLE_NAME AS Nombre_Tabla
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_TYPE = 'BASE TABLE'
  AND TABLE_SCHEMA = 'dbo'
ORDER BY TABLE_NAME;
GO