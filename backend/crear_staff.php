<?php
require_once __DIR__ . '/funciones.php';
requerirPermisoAdmin('staff');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = crearStaff($_POST, $_FILES['imagen'] ?? null);
    if (!empty($resultado['ok'])) {
        header("Location: ../admin/staff.php?ok=1");
        exit;
    }

    header("Location: ../admin/staff.php?new=1&err=" . urlencode((string)($resultado['error'] ?? 'save_failed')));
    exit;
}

header("Location: ../admin/staff.php");
exit;
