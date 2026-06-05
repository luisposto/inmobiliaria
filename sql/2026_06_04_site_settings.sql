CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(100) NOT NULL PRIMARY KEY,
    setting_value TEXT DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO site_settings (setting_key, setting_value) VALUES
    ('home_phone', '+54 341 555-1234'),
    ('home_email', 'contacto@inmobiliariaargentina.com'),
    ('home_address', 'Bv. Oroño 845, Rosario'),
    ('home_instagram_url', '#'),
    ('home_facebook_url', '#'),
    ('home_whatsapp_url', '#'),
    ('home_video_file', 'hero-home.mp4')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
