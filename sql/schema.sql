-- Esquema normalizado para inmobiliaria
-- Usuarios, operaciones, tipos de propiedad, estados y propiedades con FKs

-- CREATE DATABASE inmobiliaria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE inmobiliaria;

DROP TABLE IF EXISTS propiedad_imagenes;
DROP TABLE IF EXISTS propiedad_caracteristicas;
DROP TABLE IF EXISTS usuario_permisos;
DROP TABLE IF EXISTS site_settings;
DROP TABLE IF EXISTS staff;
DROP TABLE IF EXISTS iconos_caracteristica;
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
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuarios (nombre, email, password_hash, activo)
VALUES ('Admin', 'admin@inmobiliaria.com', SHA2('123456', 256), 1)
ON DUPLICATE KEY UPDATE email = email;

CREATE TABLE usuario_permisos (
    usuario_id INT NOT NULL,
    seccion VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id, seccion),
    CONSTRAINT fk_usuario_permiso_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

INSERT INTO usuario_permisos (usuario_id, seccion)
SELECT u.id, permisos.seccion
FROM usuarios u
CROSS JOIN (
    SELECT 'propiedades' AS seccion
    UNION ALL SELECT 'iconos'
    UNION ALL SELECT 'staff'
    UNION ALL SELECT 'usuarios'
    UNION ALL SELECT 'configuraciones'
) AS permisos;

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

CREATE TABLE iconos_caracteristica (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(80) NOT NULL UNIQUE,
    nombre VARCHAR(120) NOT NULL,
    archivo VARCHAR(255) DEFAULT NULL,
    orden INT NOT NULL DEFAULT 1,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO iconos_caracteristica (clave, nombre, archivo, orden, activo)
VALUES
    ('rooms', 'Ambientes', NULL, 1, 1),
    ('bed', 'Dormitorio', NULL, 2, 1),
    ('bath', 'Bano', NULL, 3, 1),
    ('garage', 'Cochera', NULL, 4, 1),
    ('area', 'Superficie', NULL, 5, 1),
    ('calendar', 'Estado', NULL, 6, 1),
    ('view', 'Vista', NULL, 7, 1),
    ('home', 'General', NULL, 8, 1),
    ('building', 'Edificio', NULL, 9, 1),
    ('check', 'Check', NULL, 10, 1);

CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    puesto VARCHAR(140) NOT NULL,
    descripcion TEXT NOT NULL,
    imagen VARCHAR(255) DEFAULT NULL,
    facebook_url VARCHAR(255) DEFAULT NULL,
    twitter_url VARCHAR(255) DEFAULT NULL,
    instagram_url VARCHAR(255) DEFAULT NULL,
    orden INT NOT NULL DEFAULT 1,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO staff (nombre, puesto, descripcion, imagen, facebook_url, twitter_url, instagram_url, orden, activo)
VALUES
    ('Martina Ruiz', 'Asesora comercial', 'Acompana la busqueda de propiedades, coordina visitas y ordena cada oportunidad para que el proceso sea simple y concreto.', 'staff-martina.png', '#', '#', '#', 1, 1),
    ('Valentina Gomez', 'Especialista en alquileres', 'Trabaja con propietarios e inquilinos para resolver dudas rapido, ordenar condiciones y encontrar opciones acordes a cada necesidad.', 'staff-valentina.png', '#', '#', '#', 2, 1),
    ('Leonardo Perez', 'Director de operaciones', 'Coordina negociaciones, documentacion y cierres para que cada operacion avance con orden, respaldo y tiempos claros.', 'staff-leonardo.png', '#', '#', '#', 3, 1);

CREATE TABLE site_settings (
    setting_key VARCHAR(100) NOT NULL PRIMARY KEY,
    setting_value TEXT DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO site_settings (setting_key, setting_value)
VALUES
    ('home_phone', '+54 341 555-1234'),
    ('home_email', 'contacto@inmobiliariaargentina.com'),
    ('home_address', 'Bv. Oroño 845, Rosario'),
    ('home_instagram_url', '#'),
    ('home_facebook_url', '#'),
    ('home_whatsapp_url', '#'),
    ('home_video_file', 'hero-home.mp4');

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

CREATE TABLE propiedad_caracteristicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT NOT NULL,
    icono VARCHAR(50) NOT NULL DEFAULT 'check',
    titulo VARCHAR(120) NOT NULL,
    valor VARCHAR(180) NOT NULL,
    orden INT NOT NULL DEFAULT 1,
    CONSTRAINT fk_prop_caracteristica_propiedad FOREIGN KEY (propiedad_id) REFERENCES propiedades(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE propiedad_imagenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT NOT NULL,
    archivo VARCHAR(255) NOT NULL,
    orden INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_prop_imagen_propiedad FOREIGN KEY (propiedad_id) REFERENCES propiedades(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);
