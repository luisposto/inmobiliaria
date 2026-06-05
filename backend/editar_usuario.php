<?php
require_once __DIR__ . '/funciones.php';
requerirPermisoAdmin('usuarios');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $resultado = actualizarUsuario($id, $_POST);
    if (!empty($resultado['ok'])) {
        $destino = usuarioPuedeAcceder('usuarios') ? '../admin/usuarios.php?ok=1' : obtenerRutaInicioAdmin();
        header("Location: " . $destino);
        exit;
    }

    header("Location: ../admin/usuarios.php?id={$id}&err=" . urlencode((string) ($resultado['error'] ?? 'save_failed')));
    exit;
}

header("Location: ../admin/usuarios.php");
exit;
