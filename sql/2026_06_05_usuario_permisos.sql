CREATE TABLE IF NOT EXISTS usuario_permisos (
    usuario_id INT NOT NULL,
    seccion VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id, seccion),
    CONSTRAINT fk_usuario_permiso_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

INSERT IGNORE INTO usuario_permisos (usuario_id, seccion)
SELECT u.id, permisos.seccion
FROM usuarios u
CROSS JOIN (
    SELECT 'propiedades' AS seccion
    UNION ALL SELECT 'iconos'
    UNION ALL SELECT 'staff'
    UNION ALL SELECT 'usuarios'
    UNION ALL SELECT 'configuraciones'
) AS permisos;
