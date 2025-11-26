<?php
require_once __DIR__ . '/funciones.php';
requerirLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin/dashboard.php');
    exit;
}

$propiedadId = isset($_POST['propiedad_id']) ? (int)$_POST['propiedad_id'] : 0;
$orden       = isset($_POST['orden']) ? (int)$_POST['orden'] : 0;

$prop = $propiedadId ? obtenerPropiedadPorId($propiedadId) : null;
if (!$prop) {
    header('Location: ../admin/dashboard.php');
    exit;
}

if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    header('Location: ../admin/imagenes_propiedad.php?id=' . $propiedadId);
    exit;
}

$nombreArchivo = guardarImagen($_FILES['imagen']);
if ($nombreArchivo) {
    agregarImagenPropiedad($propiedadId, $nombreArchivo, $orden);
}

header('Location: ../admin/imagenes_propiedad.php?id=' . $propiedadId);
exit;
