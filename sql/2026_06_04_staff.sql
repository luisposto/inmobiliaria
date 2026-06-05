CREATE TABLE IF NOT EXISTS staff (
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
SELECT 'Martina Ruiz', 'Asesora comercial', 'Acompana la busqueda de propiedades, coordina visitas y ordena cada oportunidad para que el proceso sea simple y concreto.', 'staff-martina.png', '#', '#', '#', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM staff WHERE orden = 1);

INSERT INTO staff (nombre, puesto, descripcion, imagen, facebook_url, twitter_url, instagram_url, orden, activo)
SELECT 'Valentina Gomez', 'Especialista en alquileres', 'Trabaja con propietarios e inquilinos para resolver dudas rapido, ordenar condiciones y encontrar opciones acordes a cada necesidad.', 'staff-valentina.png', '#', '#', '#', 2, 1
WHERE NOT EXISTS (SELECT 1 FROM staff WHERE orden = 2);

INSERT INTO staff (nombre, puesto, descripcion, imagen, facebook_url, twitter_url, instagram_url, orden, activo)
SELECT 'Leonardo Perez', 'Director de operaciones', 'Coordina negociaciones, documentacion y cierres para que cada operacion avance con orden, respaldo y tiempos claros.', 'staff-leonardo.png', '#', '#', '#', 3, 1
WHERE NOT EXISTS (SELECT 1 FROM staff WHERE orden = 3);
