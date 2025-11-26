<?php
require_once __DIR__ . '/../backend/funciones.php';

$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$prop = $id ? obtenerPropiedadPorId($id) : null;

if (!$prop) {
    header("Location: propiedades.php");
    exit;
}

$precioArs = $prop['precio'] ?? 0;
$precioUsd = $prop['precio_usd'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($prop['titulo']) ?> | Inmobiliaria Argentina</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <span class="badge rounded-circle bg-success me-2 p-2">IA</span>
            <span>
                <div class="fw-semibold">Inmobiliaria Argentina</div>
                <small class="text-light text-opacity-75 d-block">Propiedades en CABA y GBA</small>
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNavbar" aria-controls="mainNavbar"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 small">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="propiedades.php">Propiedades</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mapa.php">Mapa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contacto.php">Contacto</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container my-4">

    <a href="propiedades.php" class="text-decoration-none small text-muted d-inline-flex align-items-center mb-3">
        <span class="me-1">&#8592;</span> Volver al listado
    </a>

    <div class="row g-4">
        <!-- Columna principal -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <?php
                // Carrusel: imagen principal + imágenes adicionales desde BD
                $imagenesCarrusel = [];

                if (!empty($prop['imagen'])) {
                    $imagenesCarrusel[] = 'img/' . $prop['imagen'];
                }

                $imagenesExtras = obtenerImagenesPropiedad($id);
                if ($imagenesExtras) {
                    foreach ($imagenesExtras as $img) {
                        $imagenesCarrusel[] = 'img/' . $img['ruta'];
                    }
                }
                ?>

                <?php if (!empty($imagenesCarrusel)): ?>
                    <div id="propiedadCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php foreach ($imagenesCarrusel as $indexImg => $srcImg): ?>
                                <div class="carousel-item <?= $indexImg === 0 ? 'active' : '' ?>">
                                    <img src="<?= htmlspecialchars($srcImg) ?>" class="d-block w-100"
                                         alt="<?= htmlspecialchars($prop['titulo']) ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (count($imagenesCarrusel) > 1): ?>
                            <button class="carousel-control-prev" type="button"
                                    data-bs-target="#propiedadCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Anterior</span>
                            </button>
                            <button class="carousel-control-next" type="button"
                                    data-bs-target="#propiedadCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Siguiente</span>
                            </button>

                            <div class="carousel-indicators">
                                <?php foreach ($imagenesCarrusel as $indexImg => $srcImg): ?>
                                    <button type="button"
                                            data-bs-target="#propiedadCarousel"
                                            data-bs-slide-to="<?= $indexImg ?>"
                                            class="<?= $indexImg === 0 ? 'active' : '' ?>"
                                            aria-current="<?= $indexImg === 0 ? 'true' : 'false' ?>"
                                            aria-label="Imagen <?= $indexImg + 1 ?>"></button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center"
                         style="height: 260px;">
                        <span class="text-muted small">Sin imagen cargada</span>
                    </div>
                <?php endif; ?>

                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-2">
                        <h1 class="h4 mb-0"><?= htmlspecialchars($prop['titulo']) ?></h1>

                        <div class="text-md-end">
                            <?php if ($precioArs > 0): ?>
                                <div class="fw-semibold text-primary">
                                    $ <?= number_format($precioArs, 0, ',', '.') ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($precioUsd > 0): ?>
                                <div class="fw-semibold text-success small">
                                    USD <?= number_format($precioUsd, 0, ',', '.') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($prop['direccion']) || !empty($prop['ciudad']) || !empty($prop['provincia'])): ?>
                        <p class="text-muted small mb-2">
                            <?php if (!empty($prop['direccion'])): ?>
                                <?= htmlspecialchars($prop['direccion']) ?><br>
                            <?php endif; ?>
                            <?php if (!empty($prop['ciudad'])): ?>
                                <?= htmlspecialchars($prop['ciudad']) ?>
                            <?php endif; ?>
                            <?php if (!empty($prop['provincia'])): ?>
                                <?php if (!empty($prop['ciudad'])): ?>, <?php endif; ?>
                                <?= htmlspecialchars($prop['provincia']) ?>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>

                    <div class="mb-3">
                        <?php if (!empty($prop['operacion_nombre'])): ?>
                            <span class="badge rounded-pill text-bg-<?=
                                ($prop['operacion_nombre'] === 'Venta') ? 'success' : 'info'
                            ?> me-1">
                                <?= htmlspecialchars($prop['operacion_nombre']) ?>
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($prop['tipo_nombre'])): ?>
                            <span class="badge rounded-pill text-bg-secondary me-1">
                                <?= htmlspecialchars($prop['tipo_nombre']) ?>
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($prop['estado_nombre'])): ?>
                            <span class="badge rounded-pill text-bg-warning text-dark">
                                <?= htmlspecialchars($prop['estado_nombre']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($prop['descripcion'])): ?>
                        <h2 class="h6">Descripción</h2>
                        <p class="small mb-0">
                            <?= nl2br(htmlspecialchars($prop['descripcion'])) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h2 class="h6 mb-3">Características</h2>
                    <div class="row g-2 small">
                        <?php if (!empty($prop['superficie'])): ?>
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-2 h-100">
                                    <div class="text-muted">Superficie</div>
                                    <div class="fw-semibold"><?= (int)$prop['superficie'] ?> m²</div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($prop['ambientes'])): ?>
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-2 h-100">
                                    <div class="text-muted">Ambientes</div>
                                    <div class="fw-semibold"><?= (int)$prop['ambientes'] ?></div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($prop['banios'])): ?>
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-2 h-100">
                                    <div class="text-muted">Baños</div>
                                    <div class="fw-semibold"><?= (int)$prop['banios'] ?></div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($prop['cochera'])): ?>
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-2 h-100">
                                    <div class="text-muted">Cocheras</div>
                                    <div class="fw-semibold"><?= (int)$prop['cochera'] ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($prop['lat']) && !empty($prop['lng'])): ?>
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <h2 class="h6 mb-3">Ubicación aproximada</h2>
            <p class="text-muted small mb-2">
                El marcador indica una ubicación aproximada, no la dirección exacta.
            </p>

            <div id="map" style="height: 350px; width: 100%; border-radius: 8px;"></div>

            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    var lat = <?= $prop['lat'] ?>;
                    var lng = <?= $prop['lng'] ?>;

                    var map = L.map('map').setView([lat, lng], 15);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    L.marker([lat, lng]).addTo(map)
                        .bindPopup('Ubicación aproximada')
                        .openPopup();
                });
            </script>
        </div>
    </div>
<?php endif; ?>    </div>

        <!-- Columna lateral -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h2 class="h6 mb-3">Información de la propiedad</h2>
                    <ul class="list-unstyled small mb-0">
                        <?php if (!empty($prop['ciudad'])): ?>
                            <li class="mb-1">
                                <span class="text-muted">Ciudad:</span>
                                <span class="fw-semibold ms-1"><?= htmlspecialchars($prop['ciudad']) ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($prop['provincia'])): ?>
                            <li class="mb-1">
                                <span class="text-muted">Provincia:</span>
                                <span class="fw-semibold ms-1"><?= htmlspecialchars($prop['provincia']) ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($prop['pais'])): ?>
                            <li class="mb-1">
                                <span class="text-muted">País:</span>
                                <span class="fw-semibold ms-1"><?= htmlspecialchars($prop['pais']) ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($prop['tipo_nombre'])): ?>
                            <li class="mb-1">
                                <span class="text-muted">Tipo:</span>
                                <span class="fw-semibold ms-1"><?= htmlspecialchars($prop['tipo_nombre']) ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($prop['operacion_nombre'])): ?>
                            <li class="mb-1">
                                <span class="text-muted">Operación:</span>
                                <span class="fw-semibold ms-1"><?= htmlspecialchars($prop['operacion_nombre']) ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h6 mb-3">Consultar por esta propiedad</h2>
                    <form action="#" method="post" class="small">
                        <div class="mb-2">
                            <label class="form-label mb-1">Nombre</label>
                            <input type="text" name="nombre" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label class="form-label mb-1">Teléfono</label>
                            <input type="text" name="telefono" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label class="form-label mb-1">Email</label>
                            <input type="email" name="email" class="form-control form-control-sm">
                        </div>
                        <div class="mb-3">
                            <label class="form-label mb-1">Mensaje</label>
                            <textarea name="mensaje" rows="3" class="form-control form-control-sm">Hola, estoy interesado en la propiedad "<?= htmlspecialchars($prop['titulo']) ?>". Me gustaría recibir más información.</textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-sm">
                                Enviar consulta
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</main>

<footer class="bg-dark text-light py-3 mt-4">
    <div class="container d-flex flex-column flex-md-row justify-content-between gap-2 small">
        <span>© <?= date('Y'); ?> Inmobiliaria Argentina. Todos los derechos reservados.</span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
