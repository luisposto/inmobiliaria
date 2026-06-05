<?php
require_once __DIR__ . '/funciones.php';
requerirPermisoAdmin('ubicaciones');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = crearCiudad($_POST);
    if (!empty($resultado['ok'])) {
        header("Location: ../admin/ciudades.php?ok=1");
        exit;
    }

    header("Location: ../admin/ciudades.php?new=1&err=" . urlencode((string) ($resultado['error'] ?? 'save_failed')));
    exit;
}

header("Location: ../admin/ciudades.php");
exit;
?>
