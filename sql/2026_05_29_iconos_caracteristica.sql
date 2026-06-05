CREATE TABLE IF NOT EXISTS iconos_caracteristica (
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
    ('check', 'Check', NULL, 10, 1)
ON DUPLICATE KEY UPDATE
    nombre = VALUES(nombre),
    orden = VALUES(orden),
    activo = VALUES(activo);
