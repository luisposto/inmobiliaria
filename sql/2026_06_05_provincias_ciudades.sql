CREATE TABLE IF NOT EXISTS provincias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL UNIQUE,
    orden INT NOT NULL DEFAULT 1,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ciudades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provincia_id INT NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_ciudades_provincia_nombre (provincia_id, nombre),
    CONSTRAINT fk_ciudad_provincia FOREIGN KEY (provincia_id) REFERENCES provincias(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
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
    ('Ciudad Autonoma de Buenos Aires', 24, 1)
ON DUPLICATE KEY UPDATE
    orden = VALUES(orden),
    activo = VALUES(activo);

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
    ON seed.provincia = p.nombre
ON DUPLICATE KEY UPDATE
    activo = VALUES(activo);

INSERT INTO usuario_permisos (usuario_id, seccion)
SELECT id, 'ubicaciones'
FROM usuarios
ON DUPLICATE KEY UPDATE seccion = VALUES(seccion);
