<?php
require_once __DIR__ . '/funciones.php';
requerirLogin();

// Tomamos mismos filtros que el dashboard
$filtros = [
    'ciudad'        => $_GET['ciudad'] ?? '',
    'operacion_id'  => $_GET['operacion_id'] ?? '',
    'tipo_id'       => $_GET['tipo_id'] ?? '',
    'estado_id'     => $_GET['estado_id'] ?? '',
    'precio_min'    => $_GET['precio_min'] ?? '',
    'precio_max'    => $_GET['precio_max'] ?? '',
];

$propiedades = buscarPropiedades($filtros);

// Headers para CSV
$filename = 'propiedades_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header("Pragma: no-cache");
header("Expires: 0");

// BOM para que Excel abra en UTF-8 sin problemas
echo chr(0xEF) . chr(0xBB) . chr(0xBF);

$fp = fopen('php://output', 'w');

// Encabezados
fputcsv($fp, [
    'ID',
    'Título',
    'Dirección',
    'Ciudad',
    'Provincia',
    'País',
    'Tipo',
    'Operación',
    'Estado',
    'Precio_ARS',
    'Precio_USD',
    'Ambientes',
    'Baños',
    'Cochera',
    'Superficie_m2',
    'Lat',
    'Lng',
    'Destacada',
    'Creado'
], ';');

foreach ($propiedades as $p) {
    fputcsv($fp, [
        $p['id'],
        $p['titulo'],
        $p['direccion'],
        $p['ciudad'],
        $p['provincia'],
        $p['pais'],
        $p['tipo_nombre'] ?? '',
        $p['operacion_nombre'] ?? '',
        $p['estado_nombre'] ?? '',
        $p['precio'],
        $p['precio_usd'],
        $p['ambientes'],
        $p['banios'],
        $p['cochera'] ? 'Sí' : 'No',
        $p['superficie'],
        $p['lat'],
        $p['lng'],
        $p['destacado'] ? 'Sí' : 'No',
        $p['created_at'] ?? ''
    ], ';');
}

fclose($fp);
exit;
?>
