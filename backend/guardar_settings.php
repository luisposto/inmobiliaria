<?php
require_once __DIR__ . '/funciones.php';
requerirPermisoAdmin('configuraciones');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = guardarAjustesSitio($_POST, $_FILES['home_video'] ?? null);
    if (!empty($resultado['ok'])) {
        header("Location: ../admin/settings.php?ok=1");
        exit;
    }

    header("Location: ../admin/settings.php?err=" . urlencode((string) ($resultado['error'] ?? 'save_failed')));
    exit;
}

header("Location: ../admin/settings.php");
exit;
