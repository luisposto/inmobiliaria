<?php
require_once __DIR__ . '/funciones.php';
requerirPermisoAdmin('propiedades');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $propiedadId = isset($_POST['propiedad_id']) ? (int)$_POST['propiedad_id'] : 0;
    $imagenId = isset($_POST['imagen_id']) ? (int)$_POST['imagen_id'] : 0;

    if ($propiedadId > 0 && $imagenId > 0) {
        $cantidadAntes = count(obtenerImagenesPropiedad($propiedadId));
        eliminarImagenesPropiedadPorIds($propiedadId, [$imagenId]);
        $cantidadDespues = count(obtenerImagenesPropiedad($propiedadId));
        $ok = $cantidadDespues < $cantidadAntes;

        header("Location: ../admin/editar_propiedad.php?id={$propiedadId}&gal_del=" . ($ok ? 1 : 0) . "#galeria-fotos");
        exit;
    }
}

header("Location: ../admin/dashboard.php");
exit;
?>
