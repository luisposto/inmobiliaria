<?php
require_once __DIR__ . '/../backend/funciones.php';
$destacadas   = obtenerPropiedadesDestacadas(6);
$operaciones  = obtenerOperaciones();
$tipos        = obtenerTiposPropiedad();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inmobiliaria Argentina | Inicio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="css/styles.css">
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
                    <a class="nav-link active" href="index.php">Inicio</a>
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

<!-- CONTENIDO -->
<main class="container my-4">

    <!-- Hero + buscador -->
    <div class="row align-items-center g-4 mb-4">
        <div class="col-md-7">
            <h1 class="fw-bold mb-3">
                Encontrá tu próxima propiedad en Buenos Aires
            </h1>
            <p class="text-muted mb-4">
                Casas, departamentos y PH en venta y alquiler. Filtrá por zona, precio y tipo de propiedad.
            </p>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <a href="propiedades.php?operacion_id=1" class="btn btn-outline-secondary btn-sm">
                    En venta
                </a>
                <a href="propiedades.php?operacion_id=2" class="btn btn-outline-secondary btn-sm">
                    En alquiler
                </a>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="propiedades.php" class="btn btn-primary">
                    Ver todas las propiedades
                </a>
                <a href="mapa.php" class="btn btn-outline-secondary">
                    Ver mapa
                </a>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Buscá por filtros rápidos
                </div>
                <div class="card-body">
                    <form method="GET" action="propiedades.php" class="row g-2">
                        <div class="col-12">
                            <label class="form-label mb-1">Ciudad / barrio</label>
                            <input type="text" name="ciudad" class="form-control"
                                   placeholder="Ej: Palermo, San Isidro">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1">Operación</label>
                            <select name="operacion_id" class="form-select">
                                <option value="">Cualquiera</option>
                                <?php foreach ($operaciones as $op): ?>
                                    <option value="<?= $op['id'] ?>">
                                        <?= htmlspecialchars($op['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1">Tipo</label>
                            <select name="tipo_id" class="form-select">
                                <option value="">Cualquiera</option>
                                <?php foreach ($tipos as $t): ?>
                                    <option value="<?= $t['id'] ?>">
                                        <?= htmlspecialchars($t['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1">Precio mín. (ARS)</label>
                            <input type="number" name="precio_min" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1">Precio máx. (ARS)</label>
                            <input type="number" name="precio_max" class="form-control">
                        </div>
                        <div class="col-12 d-flex justify-content-end mt-2">
                            <button type="submit" class="btn btn-primary btn-sm me-2">
                                Filtrar
                            </button>
                            <a href="propiedades.php" class="btn btn-outline-secondary btn-sm">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Propiedades destacadas -->
    <section class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="h5 mb-0">Propiedades destacadas</h2>
            <a href="propiedades.php" class="btn btn-link btn-sm">Ver todas</a>
        </div>

        <?php if (empty($destacadas)): ?>
            <div class="alert alert-info mb-0">
                No hay propiedades destacadas por el momento.
            </div>
        
        <?php else: ?>
            <div id="destacadasCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    $porSlide = 3;
                    $total = count($destacadas);
                    $i = 0;
                    foreach ($destacadas as $prop):
                        if ($i % $porSlide === 0):
                            $active = ($i === 0) ? 'active' : '';
                    ?>
                            <div class="carousel-item <?= $active ?>">
                                <div class="row row-cols-1 row-cols-md-3 g-3">
                    <?php endif; ?>

                                    <div class="col">
                                        <div class="card h-100 shadow-sm">
                                            <?php
                                            $imagenDestacada = !empty($prop['imagen'])
                                                ? $prop['imagen']
                                                : 'prop_default.jpg';
                                            ?>
                                            <img src="img/<?= htmlspecialchars($imagenDestacada) ?>"
                                                 class="card-img-top"
                                                 alt="<?= htmlspecialchars($prop['titulo'] ?? 'Propiedad') ?>">
                                            <div class="card-body">
                                                <h5 class="card-title mb-1">
                                                    <?= htmlspecialchars($prop['titulo'] ?? 'Propiedad') ?>
                                                </h5>
                                                <p class="card-text small text-muted mb-2">
                                                    <?= htmlspecialchars($prop['tipo_nombre'] ?? '') ?>
                                                    <?php if (!empty($prop['operacion_nombre'])): ?>
                                                        · <?= htmlspecialchars($prop['operacion_nombre']) ?>
                                                    <?php endif; ?><br>
                                                    <?= htmlspecialchars($prop['ciudad'] ?? '') ?>
                                                </p>
                                                <p class="mb-0 fw-semibold">
                                                    <?php if (!empty($prop['precio'])): ?>
                                                        $<?= number_format($prop['precio'], 0, ',', '.') ?>
                                                    <?php endif; ?>
                                                    <?php if (!empty($prop['precio_usd'])): ?>
                                                        <span class="text-secondary ms-1">
                                                            (USD <?= number_format($prop['precio_usd'], 0, ',', '.') ?>)
                                                        </span>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                            <div class="card-footer bg-transparent border-0">
                                                <a href="propiedad.php?id=<?= $prop['id'] ?>"
                                                   class="btn btn-sm btn-outline-primary w-100">
                                                    Ver detalle
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                    <?php
                        $i++;
                        if ($i % $porSlide === 0 || $i === $total):
                    ?>
                                </div>
                            </div>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </div>

                <button class="carousel-control-prev" type="button"
                        data-bs-target="#destacadasCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button"
                        data-bs-target="#destacadasCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>

            </div>
        <?php endif; ?>

    </section>

</main>

<footer class="bg-dark text-light py-3 mt-4">
    <div class="container d-flex flex-column flex-md-row justify-content-between gap-2 small">
        <span>© <?= date('Y'); ?> Inmobiliaria Argentina. Todos los derechos reservados.</span>
        <span>
            Panel admin:
            <a href="../admin/login.php" class="link-light text-decoration-underline">Acceder</a>
        </span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Botón flotante WhatsApp -->
<a href="https://wa.me/5491140678136?text=Hola%20quiero%20más%20información" 
   class="whatsapp-float" 
   target="_blank">
    <img src="https://cdn-icons-png.flaticon.com/512/124/124034.png" 
         alt="Enviar mensaje por WhatsApp" 
         class="whatsapp-icon">
</a>
</body>
</html>
