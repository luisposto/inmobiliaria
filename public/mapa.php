<?php
require_once __DIR__ . '/../backend/funciones.php';

$operaciones = obtenerOperaciones();
$tipos = obtenerTiposPropiedad();
$filtros = [
    'ciudad'        => $_GET['ciudad'] ?? '',
    'operacion_id'  => $_GET['operacion_id'] ?? '',
    'tipo_id'       => $_GET['tipo_id'] ?? '',
    'estado_id'     => $_GET['estado_id'] ?? '',
    'moneda'        => strtoupper($_GET['moneda'] ?? 'ARS'),
    'precio_min'    => $_GET['precio_min'] ?? '',
    'precio_max'    => $_GET['precio_max'] ?? '',
];
$propiedades = buscarPropiedades($filtros);

$defaultLat = -34.6037;
$defaultLng = -58.3816;

foreach ($propiedades as $p) {
    if (!empty($p['lat']) && !empty($p['lng'])) {
        $defaultLat = $p['lat'];
        $defaultLng = $p['lng'];
        break;
    }
}

$propsJson = json_encode(array_map(function($p) {
    return [
        'id' => $p['id'],
        'titulo' => $p['titulo'],
        'direccion' => $p['direccion'],
        'ciudad' => $p['ciudad'],
        'precio' => $p['precio'],
        'precio_usd' => $p['precio_usd'],
        'operacion' => $p['operacion_nombre'] ?? '',
        'estado' => $p['estado_nombre'] ?? '',
        'lat' => $p['lat'],
        'lng' => $p['lng'],
    ];
}, $propiedades));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mapa de propiedades - Inmobiliaria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= htmlspecialchars(publicAssetUrl('css/tailwind.css')) ?>?v=<?= publicAssetVersion('css/tailwind.css') ?>" rel="stylesheet">
    <link href="<?= htmlspecialchars(publicAssetUrl('css/theme-overrides.css')) ?>?v=<?= publicAssetVersion('css/theme-overrides.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map {
            height: calc(100vh - 21rem);
            min-height: 32rem;
        }

        @media (max-width: 768px) {
            #map {
                height: 60vh;
                min-height: 22rem;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
<header class="app-header">
    <div class="app-header-inner">
        <a href="index.php" class="app-brand">
            <span class="app-brand-mark">IA</span>
            <div class="app-brand-copy">
                <p class="app-brand-title">Inmobiliaria Argentina</p>
                <p class="app-brand-subtitle">Mapa interactivo</p>
            </div>
        </a>
        <nav class="app-nav">
            <a href="index.php">Inicio</a>
            <a href="propiedades.php">Propiedades</a>
            <a href="mapa.php">Mapa</a>
            <a href="contacto.php">Contacto</a>
        </nav>
    </div>
</header>

<main class="app-main space-y-4">
    <section class="hero-panel">
        <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <span class="eyebrow">Visualización</span>
                <h1 class="section-heading mb-2">Mapa de propiedades</h1>
                <p class="section-copy">Explorá la ubicación de las propiedades y abrí cada ficha desde el mapa.</p>
            </div>
            <a href="propiedades.php" class="btn-secondary text-xs md:text-sm">Ver en listado</a>
        </div>

        <form method="GET" class="grid gap-3 text-xs md:grid-cols-3 md:text-sm xl:grid-cols-6">
            <input type="text" name="ciudad" value="<?= htmlspecialchars($filtros['ciudad']) ?>" placeholder="Ciudad / barrio"
                   class="field-input xl:col-span-2">
            <select name="operacion_id" class="field-input">
                <option value="">Operación</option>
                <?php foreach ($operaciones as $op): ?>
                    <option value="<?= $op['id'] ?>" <?= ($filtros['operacion_id'] == $op['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($op['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="tipo_id" class="field-input">
                <option value="">Tipo</option>
                <?php foreach ($tipos as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= ($filtros['tipo_id'] == $t['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="moneda" class="field-input">
                <option value="ARS" <?= $filtros['moneda'] === 'ARS' ? 'selected' : '' ?>>Pesos</option>
                <option value="USD" <?= $filtros['moneda'] === 'USD' ? 'selected' : '' ?>>USD</option>
            </select>
            <input type="number" name="precio_min" value="<?= htmlspecialchars($filtros['precio_min']) ?>" placeholder="Precio mín."
                   class="field-input">
            <input type="number" name="precio_max" value="<?= htmlspecialchars($filtros['precio_max']) ?>" placeholder="Precio máx."
                   class="field-input">
            <div class="flex flex-col gap-2 pt-1 sm:flex-row xl:col-span-6">
                <button class="btn-primary rounded-2xl">Filtrar</button>
                <a href="mapa.php" class="btn-secondary rounded-2xl">Limpiar</a>
            </div>
        </form>
    </section>

    <div class="surface-card overflow-hidden p-2">
        <div id="map" class="w-full rounded-[1.4rem]"></div>
    </div>
</main>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
<script>
    const defaultLat = <?= json_encode($defaultLat) ?>;
    const defaultLng = <?= json_encode($defaultLng) ?>;
    const propiedades = <?= $propsJson ?>;

    const map = L.map('map').setView([defaultLat, defaultLng], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    propiedades.forEach(p => {
        if (!p.lat || !p.lng) return;

        const marker = L.marker([p.lat, p.lng]).addTo(map);

        const precio = new Intl.NumberFormat('es-AR', { maximumFractionDigits: 0 }).format(p.precio || 0);
        const precioUsd = p.precio_usd ? new Intl.NumberFormat('es-AR', { maximumFractionDigits: 0 }).format(p.precio_usd) : null;

        let html = `<strong>${p.titulo}</strong><br>`;
        if (p.direccion) html += `${p.direccion}<br>`;
        if (p.ciudad) html += `${p.ciudad}<br>`;
        if (p.operacion) html += `<span>${p.operacion}</span><br>`;
        if (p.estado) html += `<span>Estado: ${p.estado}</span><br>`;
        if (precioUsd) {
            html += `U$S ${precioUsd}<br>$ ${precio}`;
        } else {
            html += `$ ${precio}`;
        }
        html += `<br><a href="propiedad.php?id=${p.id}" class="text-emerald-700" target="_blank">Ver detalle</a>`;

        marker.bindPopup(html);
    });
</script>
</body>
</html>
