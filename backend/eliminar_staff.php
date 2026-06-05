<?php
require_once __DIR__ . '/funciones.php';
requerirPermisoAdmin('staff');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $resultado = eliminarStaff($id);
    if (!empty($resultado['ok'])) {
        header("Location: ../admin/staff.php?del=1");
        exit;
    }

    header("Location: ../admin/staff.php?err=" . urlencode((string)($resultado['error'] ?? 'delete_failed')));
    exit;
}

header("Location: ../admin/staff.php");
exit;
