<?php
require_once __DIR__ . '/funciones.php';
requerirPermisoAdmin('ubicaciones');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($id > 0) {
        $resultado = actualizarCiudad($id, $_POST);
        if (!empty($resultado['ok'])) {
            header("Location: ../admin/ciudades.php?ok=1");
            exit;
        }

        header("Location: ../admin/ciudades.php?id={$id}&err=" . urlencode((string) ($resultado['error'] ?? 'save_failed')));
        exit;
    }
}

header("Location: ../admin/ciudades.php");
exit;
?>
