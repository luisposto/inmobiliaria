<?php
require_once __DIR__ . '/funciones.php';
requerirPermisoAdmin('usuarios');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $resultado = eliminarUsuario($id);
    if (!empty($resultado['ok'])) {
        header("Location: ../admin/usuarios.php?del=1");
        exit;
    }

    header("Location: ../admin/usuarios.php?err=" . urlencode((string) ($resultado['error'] ?? 'delete_failed')));
    exit;
}

header("Location: ../admin/usuarios.php");
exit;
