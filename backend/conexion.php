<?php
// Configuración de la base de datos
$DB_HOST = '127.0.0.1';
$DB_PORT = '3307';
$DB_NAME = 'inmobiliaria';
$DB_USER = 'root';
$DB_PASS = (json_decode(file_get_contents('C:/xampp/security-private/database.json'), true)['root_password']);

$dsn = "mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
