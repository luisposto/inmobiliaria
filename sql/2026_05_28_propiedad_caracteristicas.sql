CREATE TABLE IF NOT EXISTS propiedad_caracteristicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT NOT NULL,
    icono VARCHAR(50) NOT NULL DEFAULT 'check',
    titulo VARCHAR(120) NOT NULL,
    valor VARCHAR(180) NOT NULL,
    orden INT NOT NULL DEFAULT 1,
    CONSTRAINT fk_prop_caracteristica_propiedad FOREIGN KEY (propiedad_id) REFERENCES propiedades(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);
