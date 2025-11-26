-- Esquema normalizado para inmobiliaria
-- Usuarios, operaciones, tipos de propiedad, estados y propiedades con FKs

-- CREATE DATABASE inmobiliaria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE inmobiliaria;

DROP TABLE IF EXISTS propiedades;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS operaciones;
DROP TABLE IF EXISTS tipos_propiedad;
DROP TABLE IF EXISTS estados_propiedad;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuarios (nombre, email, password_hash)
VALUES ('Admin', 'admin@inmobiliaria.com', SHA2('123456', 256))
ON DUPLICATE KEY UPDATE email = email;

CREATE TABLE operaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO operaciones (nombre)
VALUES ('Venta'), ('Alquiler'), ('Temporario'), ('Reserva'), ('Señada');

CREATE TABLE tipos_propiedad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

INSERT INTO tipos_propiedad (nombre)
VALUES 
    ('Casa'),
    ('Departamento'),
    ('PH'),
    ('Terreno'),
    ('Local'),
    ('Galpón'),
    ('Oficina'),
    ('Cochera'),
    ('Campo');

CREATE TABLE estados_propiedad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO estados_propiedad (nombre)
VALUES ('Disponible'), ('Reservada'), ('Vendida');

CREATE TABLE propiedades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(12,2) NOT NULL DEFAULT 0,
    precio_usd DECIMAL(12,2) DEFAULT 0,
    direccion VARCHAR(255),
    ciudad VARCHAR(120),
    provincia VARCHAR(120),
    pais VARCHAR(120) DEFAULT 'Argentina',
    tipo_id INT NULL,
    operacion_id INT NULL,
    estado_id INT NULL,
    ambientes INT DEFAULT 0,
    banios INT DEFAULT 0,
    cochera TINYINT(1) DEFAULT 0,
    superficie INT DEFAULT 0,
    lat DECIMAL(10,7) NULL,
    lng DECIMAL(10,7) NULL,
    imagen VARCHAR(255),
    destacado TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_prop_tipo FOREIGN KEY (tipo_id) REFERENCES tipos_propiedad(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_prop_operacion FOREIGN KEY (operacion_id) REFERENCES operaciones(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_prop_estado FOREIGN KEY (estado_id) REFERENCES estados_propiedad(id)
        ON UPDATE CASCADE ON DELETE SET NULL
);
