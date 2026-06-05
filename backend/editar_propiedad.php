<?php
require_once __DIR__ . '/funciones.php';
requerirPermisoAdmin('propiedades');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id > 0) {
        $ok = actualizarPropiedad($id, $_POST, $_FILES['imagen'] ?? null, $_FILES['galeria'] ?? null);
        $_SESSION['flash_actualizacion_propiedad'] = $ok ? 'ok' : 'error';
        header("Location: ../admin/editar_propiedad.php?id={$id}");
        exit;
    }
}

header("Location: ../admin/dashboard.php");
exit;
?>
