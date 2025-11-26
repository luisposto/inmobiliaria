<?php
require_once __DIR__ . '/funciones.php';
requerirLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id > 0) {
        $ok = eliminarPropiedad($id);
        header("Location: ../admin/dashboard.php?del=" . ($ok ? 1 : 0));
        exit;
    }
}

header("Location: ../admin/dashboard.php");
exit;
?>
