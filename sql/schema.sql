-- Esquema normalizado para inmobiliaria
-- Usuarios, operaciones, tipos de propiedad, estados y propiedades con FKs

-- CREATE DATABASE inmobiliaria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE inmobiliaria;

DROP TABLE IF EXISTS propiedad_imagenes;
DROP TABLE IF EXISTS propiedad_caracteristicas;
DROP TABLE IF EXISTS ciudades;
DROP TABLE IF EXISTS provincias;
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
    UNION ALL SELECT 'ubicaciones'
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

CREATE TABLE provincias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL UNIQUE,
    orden INT NOT NULL DEFAULT 1,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO provincias (nombre, orden, activo)
VALUES
    ('Buenos Aires', 1, 1),
    ('Catamarca', 2, 1),
    ('Chaco', 3, 1),
    ('Chubut', 4, 1),
    ('Cordoba', 5, 1),
    ('Corrientes', 6, 1),
    ('Entre Rios', 7, 1),
    ('Formosa', 8, 1),
    ('Jujuy', 9, 1),
    ('La Pampa', 10, 1),
    ('La Rioja', 11, 1),
    ('Mendoza', 12, 1),
    ('Misiones', 13, 1),
    ('Neuquen', 14, 1),
    ('Rio Negro', 15, 1),
    ('Salta', 16, 1),
    ('San Juan', 17, 1),
    ('San Luis', 18, 1),
    ('Santa Cruz', 19, 1),
    ('Santa Fe', 20, 1),
    ('Santiago del Estero', 21, 1),
    ('Tierra del Fuego', 22, 1),
    ('Tucuman', 23, 1),
    ('Ciudad Autonoma de Buenos Aires', 24, 1);

CREATE TABLE ciudades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provincia_id INT NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_ciudades_provincia_nombre (provincia_id, nombre),
    CONSTRAINT fk_ciudad_provincia FOREIGN KEY (provincia_id) REFERENCES provincias(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

INSERT INTO ciudades (provincia_id, nombre, activo)
SELECT p.id, seed.nombre, 1
FROM provincias p
INNER JOIN (
    SELECT 'Buenos Aires' AS provincia, 'La Plata' AS nombre
    UNION ALL SELECT 'Buenos Aires', 'Mar del Plata'
    UNION ALL SELECT 'Buenos Aires', 'Bahia Blanca'
    UNION ALL SELECT 'Buenos Aires', 'San Nicolas de los Arroyos'
    UNION ALL SELECT 'Catamarca', 'San Fernando del Valle de Catamarca'
    UNION ALL SELECT 'Catamarca', 'Belen'
    UNION ALL SELECT 'Chaco', 'Resistencia'
    UNION ALL SELECT 'Chaco', 'Presidencia Roque Saenz Pena'
    UNION ALL SELECT 'Chubut', 'Rawson'
    UNION ALL SELECT 'Chubut', 'Comodoro Rivadavia'
    UNION ALL SELECT 'Chubut', 'Puerto Madryn'
    UNION ALL SELECT 'Cordoba', 'Cordoba'
    UNION ALL SELECT 'Cordoba', 'Villa Carlos Paz'
    UNION ALL SELECT 'Cordoba', 'Rio Cuarto'
    UNION ALL SELECT 'Cordoba', 'Villa Maria'
    UNION ALL SELECT 'Corrientes', 'Corrientes'
    UNION ALL SELECT 'Corrientes', 'Goya'
    UNION ALL SELECT 'Entre Rios', 'Parana'
    UNION ALL SELECT 'Entre Rios', 'Concordia'
    UNION ALL SELECT 'Entre Rios', 'Gualeguaychu'
    UNION ALL SELECT 'Formosa', 'Formosa'
    UNION ALL SELECT 'Formosa', 'Clorinda'
    UNION ALL SELECT 'Jujuy', 'San Salvador de Jujuy'
    UNION ALL SELECT 'Jujuy', 'Palpala'
    UNION ALL SELECT 'La Pampa', 'Santa Rosa'
    UNION ALL SELECT 'La Pampa', 'General Pico'
    UNION ALL SELECT 'La Rioja', 'La Rioja'
    UNION ALL SELECT 'La Rioja', 'Chilecito'
    UNION ALL SELECT 'Mendoza', 'Mendoza'
    UNION ALL SELECT 'Mendoza', 'San Rafael'
    UNION ALL SELECT 'Mendoza', 'Godoy Cruz'
    UNION ALL SELECT 'Misiones', 'Posadas'
    UNION ALL SELECT 'Misiones', 'Obera'
    UNION ALL SELECT 'Misiones', 'Puerto Iguazu'
    UNION ALL SELECT 'Neuquen', 'Neuquen'
    UNION ALL SELECT 'Neuquen', 'San Martin de los Andes'
    UNION ALL SELECT 'Neuquen', 'Villa La Angostura'
    UNION ALL SELECT 'Rio Negro', 'Viedma'
    UNION ALL SELECT 'Rio Negro', 'San Carlos de Bariloche'
    UNION ALL SELECT 'Rio Negro', 'General Roca'
    UNION ALL SELECT 'Salta', 'Salta'
    UNION ALL SELECT 'Salta', 'San Ramon de la Nueva Oran'
    UNION ALL SELECT 'San Juan', 'San Juan'
    UNION ALL SELECT 'San Juan', 'Rawson'
    UNION ALL SELECT 'San Luis', 'San Luis'
    UNION ALL SELECT 'San Luis', 'Villa Mercedes'
    UNION ALL SELECT 'Santa Cruz', 'Rio Gallegos'
    UNION ALL SELECT 'Santa Cruz', 'Caleta Olivia'
    UNION ALL SELECT 'Santa Fe', 'Santa Fe'
    UNION ALL SELECT 'Santa Fe', 'Rosario'
    UNION ALL SELECT 'Santa Fe', 'Rafaela'
    UNION ALL SELECT 'Santa Fe', 'Venado Tuerto'
    UNION ALL SELECT 'Santiago del Estero', 'Santiago del Estero'
    UNION ALL SELECT 'Santiago del Estero', 'La Banda'
    UNION ALL SELECT 'Tierra del Fuego', 'Ushuaia'
    UNION ALL SELECT 'Tierra del Fuego', 'Rio Grande'
    UNION ALL SELECT 'Tucuman', 'San Miguel de Tucuman'
    UNION ALL SELECT 'Tucuman', 'Yerba Buena'
    UNION ALL SELECT 'Ciudad Autonoma de Buenos Aires', 'Ciudad Autonoma de Buenos Aires'
) AS seed
    ON seed.provincia = p.nombre;

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
