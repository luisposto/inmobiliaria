<?php
require_once __DIR__ . '/funciones.php';
requerirPermisoAdmin('iconos');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id > 0) {
        $resultado = actualizarIconoCaracteristica($id, $_POST, $_FILES['imagen'] ?? null);
        if (!empty($resultado['ok'])) {
            header("Location: ../admin/iconos.php?ok=1");
            exit;
        }

        header("Location: ../admin/iconos.php?id={$id}&err=" . urlencode((string)($resultado['error'] ?? 'save_failed')));
        exit;
    }
}

header("Location: ../admin/iconos.php");
exit;
?>
