CREATE TABLE IF NOT EXISTS propiedad_imagenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT NOT NULL,
    archivo VARCHAR(255) NOT NULL,
    orden INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_prop_imagen_propiedad FOREIGN KEY (propiedad_id) REFERENCES propiedades(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);
