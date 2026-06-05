<?php
require_once __DIR__ . '/funciones.php';
requerirPermisoAdmin('iconos');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id > 0) {
        $resultado = eliminarIconoCaracteristica($id);
        if (!empty($resultado['ok'])) {
            header("Location: ../admin/iconos.php?del=1");
            exit;
        }

        header("Location: ../admin/iconos.php?err=" . urlencode((string)($resultado['error'] ?? 'delete_failed')));
        exit;
    }
}

header("Location: ../admin/iconos.php");
exit;
?>
