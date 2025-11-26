<?php
require_once __DIR__ . '/funciones.php';
requerirLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id > 0) {
        $ok = actualizarPropiedad($id, $_POST, $_FILES['imagen'] ?? null);
        header("Location: ../admin/dashboard.php?upd=" . ($ok ? 1 : 0));
        exit;
    }
}

header("Location: ../admin/dashboard.php");
exit;
?>
