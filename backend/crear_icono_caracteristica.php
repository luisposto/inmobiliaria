<?php
require_once __DIR__ . '/funciones.php';
requerirPermisoAdmin('iconos');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = crearIconoCaracteristica($_POST, $_FILES['imagen'] ?? null);
    if (!empty($resultado['ok'])) {
        header("Location: ../admin/iconos.php?ok=1");
        exit;
    }

    header("Location: ../admin/iconos.php?new=1&err=" . urlencode((string)($resultado['error'] ?? 'save_failed')));
    exit;
}

header("Location: ../admin/iconos.php");
exit;
?>
