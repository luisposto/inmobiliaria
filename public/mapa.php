<?php
require_once __DIR__ . '/../backend/funciones.php';

$operaciones = obtenerOperaciones();
$tipos       = obtenerTiposPropiedad();

$filtros = [
    'ciudad'        => $_GET['ciudad'] ?? '',
    'operacion_id'  => $_GET['operacion_id'] ?? '',
    'tipo_id'       => $_GET['tipo_id'] ?? '',
    'estado_id'     => $_GET['estado_id'] ?? '',
    'precio_min'    => $_GET['precio_min'] ?? '',
    'precio_max'    => $_GET['precio_max'] ?? '',
];

$propiedades = buscarPropiedades($filtros);

$propiedadesSimple = array_values(array_map(function ($p) {
    return [
        'id'         => $p['id'],
        'titulo'     => $p['titulo'] ?? 'Propiedad',
        'direccion'  => $p['direccion'] ?? '',
        'ciudad'     => $p['ciudad'] ?? '',
        'operacion'  => $p['operacion_nombre'] ?? '',
        'estado'     => $p['estado_nombre'] ?? '',
        'precio'     => $p['precio'] ?? 0,
        'precio_usd' => $p['precio_usd'] ?? 0,
        'lat'        => $p['lat'],
        'lng'        => $p['lng'],
    ];
}, $propiedades));

$queryString = http_build_query($filtros);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mapa de propiedades | Inmobiliaria Argentina</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Leaflet CSS -->
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <style>
        #map {
            height: calc(100vh - 170px);
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <span class="badge rounded-circle bg-success me-2 p-2">IA</span>
            <span>
                <div class="fw-semibold">Inmobiliaria Argentina</div>
                <small class="text-light-50">Propiedades en CABA y GBA</small>
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNavbar" aria-controls="mainNavbar"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="propiedades.php">Propiedades</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="mapa.php">Mapa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contacto.php">Contacto</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container-fluid py-3">
    <div class="container mb-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
            <h1 class="h5 mb-0">Mapa de propiedades</h1>

            <div class="d-flex flex-wrap gap-2">
                <div class="btn-group" role="group">
                    <a href="propiedades.php?<?= $queryString ?>" class="btn btn-sm btn-outline-primary">
                        📋 Ver lista
                    </a>
                    <a href="mapa.php?<?= $queryString ?>" class="btn btn-sm btn-primary">
                        🗺 Ver mapa
                    </a>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label mb-1">Ciudad / barrio</label>
                        <input type="text" name="ciudad" class="form-control"
                               value="<?= htmlspecialchars($filtros['ciudad']) ?>"
                               placeholder="Ej: Palermo, San Isidro">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1">Operación</label>
                        <select name="operacion_id" class="form-select">
                            <option value="">Todas</option>
                            <?php foreach ($operaciones as $op): ?>
                                <option value="<?= $op['id'] ?>"
                                    <?= ($filtros['operacion_id'] == $op['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($op['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1">Tipo</label>
                        <select name="tipo_id" class="form-select">
                            <option value="">Todos</option>
                            <?php foreach ($tipos as $t): ?>
                                <option value="<?= $t['id'] ?>"
                                    <?= ($filtros['tipo_id'] == $t['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1">Precio mín. (ARS)</label>
                        <input type="number" name="precio_min" class="form-control"
                               value="<?= htmlspecialchars($filtros['precio_min']) ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1">Precio máx. (ARS)</label>
                        <input type="number" name="precio_max" class="form-control"
                               value="<?= htmlspecialchars($filtros['precio_max']) ?>">
                    </div>

                    <div class="col-12 d-flex justify-content-end mt-2">
                        <button type="submit" class="btn btn-primary btn-sm me-2">
                            Filtrar
                        </button>
                        <a href="mapa.php" class="btn btn-outline-secondary btn-sm">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MAPA -->
    <div id="map" class="w-100"></div>
</main>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const propiedades = <?= json_encode($propiedadesSimple, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK) ?>;

    const map = L.map('map').setView([-34.6037, -58.3816], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    if (propiedades.length > 0) {
        const bounds = [];
        propiedades.forEach(p => {
            if (!p.lat || !p.lng) return;

            const marker = L.marker([p.lat, p.lng]).addTo(map);

            const precio = new Intl.NumberFormat('es-AR', {
                maximumFractionDigits: 0
            }).format(p.precio || 0);

            const precioUsd = p.precio_usd
                ? new Intl.NumberFormat('es-AR', {
                    maximumFractionDigits: 0
                }).format(p.precio_usd)
                : null;

            let html = `<strong>${p.titulo}</strong><br>`;
            if (p.direccion) html += `${p.direccion}<br>`;
            if (p.ciudad) html += `${p.ciudad}<br>`;
            if (p.operacion) html += `${p.operacion}<br>`;

            if (precio > 0) {
                html += `$ ${precio}`;
                if (precioUsd) {
                    html += ` (USD ${precioUsd})`;
                }
                html += '<br>';
            }

            html += `<a href="propiedad.php?id=${p.id}" target="_blank">Ver detalle</a>`;

            marker.bindPopup(html);

            bounds.push([p.lat, p.lng]);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, {padding: [30, 30]});
        }
    }
</script>

<footer class="bg-dark text-light py-3 mt-4">
    <div class="container d-flex flex-column flex-md-row justify-content-between gap-2 small">
        <span>© <?= date('Y'); ?> Inmobiliaria Argentina. Todos los derechos reservados.</span>
       </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
