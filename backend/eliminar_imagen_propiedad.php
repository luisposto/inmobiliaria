<?php
require_once __DIR__ . '/funciones.php';
requerirLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin/dashboard.php');
    exit;
}

$idImagen     = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$propiedadId  = isset($_POST['propiedad_id']) ? (int)$_POST['propiedad_id'] : 0;

$img = $idImagen ? obtenerImagenPropiedadPorId($idImagen) : null;
if ($img) {
    $rutaFs = __DIR__ . '/../public/img/' . $img['ruta'];
    if (is_file($rutaFs)) {
        @unlink($rutaFs);
    }
    eliminarImagenPropiedad($idImagen);
}

if ($propiedadId) {
    header('Location: ../admin/imagenes_propiedad.php?id=' . $propiedadId);
} else {
    header('Location: ../admin/dashboard.php');
}
exit;
