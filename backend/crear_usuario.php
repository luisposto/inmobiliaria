<?php
require_once __DIR__ . '/funciones.php';
requerirPermisoAdmin('usuarios');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = crearUsuario($_POST);
    if (!empty($resultado['ok'])) {
        header("Location: ../admin/usuarios.php?ok=1");
        exit;
    }

    header("Location: ../admin/usuarios.php?new=1&err=" . urlencode((string) ($resultado['error'] ?? 'save_failed')));
    exit;
}

header("Location: ../admin/usuarios.php");
exit;
