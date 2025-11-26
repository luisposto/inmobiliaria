<?php
require_once __DIR__ . '/funciones.php';
requerirLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = crearPropiedad($_POST, $_FILES['imagen'] ?? null);
    header("Location: ../admin/dashboard.php?ok=1");
    exit;
}

header("Location: ../admin/dashboard.php");
exit;
?>
